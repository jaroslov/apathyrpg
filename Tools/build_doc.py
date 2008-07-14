#!/usr/bin/env python2.5

import os, sys, random
from optparse import OptionParser
from lxml import etree
from random import SystemRandom
import codecs
from Uni2LaTeX import unicodeToLaTeX

FASTHACK = False

HTMLNS = """http://www.w3.org/1999/xhtml"""
HTMLNSMap = {'x':HTMLNS}

LATEX = """\\documentclass[twoside,10pt]{book}
\\usepackage{pslatex}
\\usepackage{newcent}
\\usepackage{multicol}
\\usepackage{rotating}
\\usepackage{tabularx}
\\usepackage{array}
\\usepackage{longtable}
\\usepackage{multirow}
\\usepackage{graphicx}
\\usepackage[T1]{fontenc}
\\usepackage{hyperref}
\\usepackage{wrapfig}
\\usepackage[text={7in,7.75in},textheight=7in]{geometry}
\\begin{document}

\\begin{titlepage}

\\newcounter{ExampleCounter}
  \\setcounter{ExampleCounter}{1}
  \\newcommand{\\quoteexample}[2][~] {
    \\vspace{1em}
    \\addcontentsline{lof}{section}{\\arabic{ExampleCounter} \\textsc{#1}}
    \\vbox{
      \\textsc{\\noindent Example \\arabic{ExampleCounter} {\\textbf{#1}}}
      \\begin{quotation}
        {\\small #2}
      \\end{quotation}
      \\vspace{1em}
    }
    \\addtocounter{ExampleCounter}{1}
  }

%s

\\end{titlepage}
\\setcounter{page}{1}
\\pagenumbering{roman}
\\setcounter{tocdepth}{3}
\\tableofcontents
\\newpage
\\listoftables
\\newpage
\\listoffigures
\\newpage
\\pagenumbering{arabic}
\\setcounter{page}{1}

\\begin{small}

%s

\\end{small}
\\end{document}
"""

def apathy_hash(string):
  hash = ""
  for s in string:
    hash += str(ord(s))
  return hash

def parseOptions():
  parser = OptionParser()
  parser.add_option("-p","--prefix",dest="prefix",
                    help="[required] the directory of the document",
                    metavar="FOLDER")
  parser.add_option("-o","--output",dest="output",
                    help="the name of the output file",
                    metavar="FILE")
  parser.add_option("-l","--latex",dest="latex",
                    action="store_true",help="produce LaTeX output")
  parser.add_option("-w","--xhtml",dest="xhtml",
                    action="store_true",help="produce webpage output")
  parser.add_option("","--lint",dest="lint",
                    action="store_true",
                    help="attempt to the clean the source files")
  parser.add_option("","--main-document",dest="main",
                    help="the name of the main document, defaults to 'Apathy'",
                    metavar="FILE")
  parser.add_option("","--retarget-resources",dest="retargetresources",
                    help="retarget image, css, etc. resources",
                    action="store_true")
  parser.add_option("","--time-period",dest="time_period",
                    help="keeps only those sections before a certain time (date)",
                    metavar="INTEGER")
  parser.add_option("","--category-exclusion-list",dest="exclude",
                    help="A file of which categories to exclude from the final document",
                    metavar="EXCLUSION LIST")
  parser.add_option("","--list-categories",dest="list_categories",
                    help="Lists the set of categories in the document",
                    action="store_true")

  
  (options, args) = parser.parse_args()

  if options.prefix is None:
    print >> sys.stderr, 'You must give a source directory using "--prefix=???"',
    print >> sys.stderr, "see --help"
    sys.exit(1)
  if options.output is None:
    options.output = "Apathy"
  if options.latex is None:
    options.latex = False
  if options.xhtml is None:
    options.xhtml = False
  if options.lint is None:
    options.lint = False
  if options.main is None:
    options.main = "Apathy"
  if options.retargetresources is None:
    options.retargetresources = False
  try: # convert time-period to an integer
    options.time_period = int(options.time_period)
  except:
    options.time_period = 1000000000000
  if options.exclude is None:
    options.exclude = []
  else:
    options.exclude = [s.strip() for s in open(options.exclude, "r").readlines()]
  if options.list_categories is None:
    options.list_categories = False

  return options, args

def xpath (Node, Path):
  return Node.xpath(Path, namespaces=HTMLNSMap)

def get_column(table, index):
  trows = table.xpath("//tr")
  column = []
  for trow in trows:
    if index < len(trow.getchildren()):
      column.append(trow.getchildren()[index])
    else:
      column.append(None)
  return column

def transform_summarize_table(subdoc, options):
  not_in_tables = subdoc.xpath("//th[@class!='Title' and @class!='Table']")
  not_in_columns = []
  for nit in not_in_tables:
    not_in_columns.append(get_column(subdoc, nit.getparent().index(nit)))
  thead = subdoc.xpath("//thead")[0]
  title = subdoc.xpath("//th[@class='Title']")
  if len(title) <= 0:
    return subdoc # no title; it can happen with a "dummy" table
  title = title[0]
  titledx = title.getparent().index(title)
  rows = subdoc.xpath("//tr")
  for rdx in xrange(len(rows)):
    row = rows[rdx]
    td = etree.Element("td")
    p = etree.SubElement(td, "p")
    titleptxt = row[titledx].xpath("./p")[0].text
    a = etree.SubElement(p, "a", href="#id%s"%(apathy_hash(titleptxt)))
    a.text = titleptxt
    row.replace(row[titledx], td)
    for nic in not_in_columns:
      nicol = nic[rdx]
      if nicol is not None:
        row.remove(nicol)
  for nit in not_in_tables:
    thead.remove(nit)
  return subdoc

def transform_hrid_table(subdoc, options):
  """
  Given a Category Table, convert it for display:
  (1) extract Title & Description and build per-entry information
  (2) remove all-Non-Table values
  """
  DSet = """<div class="description-set"/>"""
  Desc = """\n<div class="description">
  <h1 class="description-title" title="yes">
    <p/>
  </h1>
  <div class="description-body" description="yes">
  </div>
</div>
"""
  title = subdoc.xpath("//th[@class='Title']")[0]
  description = subdoc.xpath("//th[@class='Description']")[0]
  tables = subdoc.xpath("//th[@class='Table']")
  not_in_tables = subdoc.xpath("//th[@class!='Title' and @class!='Table']")
  rows = subdoc.xpath("//tr")
  title_column = get_column(subdoc, title.getparent().index(title))
  descr_column = get_column(subdoc, description.getparent().index(description))
  table_columns = []
  for table in tables:
    table_columns.append(get_column(subdoc, table.getparent().index(table)))
  not_in_columns = []
  for not_in_table in not_in_tables:
    not_in_columns.append(get_column(subdoc, not_in_table.getparent().index(not_in_table)))

  # Build the description set and update the table
  DSetNode = etree.fromstring(DSet)
  for rdx in xrange(len(rows)):
    row = rows[rdx]
    ## build the description set
    DescNode = etree.fromstring(Desc)
    titledx = title.getparent().index(title)
    descdx = description.getparent().index(description)
    titlep = DescNode.xpath("//h1/p")[0]
    bodydiv = DescNode.xpath("//div[@class='description-body']")[0]
    titlep.text = row.getchildren()[titledx].xpath("p")[0].text
    Nid = "id%s"%(apathy_hash(titlep.text))
    children = row.getchildren()[descdx].getchildren()
    for child in children:
      bodydiv.append(child)
    DescNode.set('id', Nid)
    DSetNode.append(DescNode)
    # remove non-table/non-title items
    for not_in_columnz in not_in_columns:
      nicol = not_in_columnz[rdx]
      if nicol is not None:
        row.remove(nicol)
    a_elt = etree.Element("a", href="#"+Nid)
    titlep = row[titledx].xpath("./p")[0]
    a_elt.text = titlep.text
    p_elt = etree.Element("p"); p_elt.append(a_elt)
    td_elt = etree.Element("td"); td_elt.append(p_elt)
    row.remove(row.getchildren()[0])
    row.insert(0, td_elt)

  ## nuke thead columns we don't want
  for nit in not_in_tables:
    title.getparent().remove(nit)

  rdiv = etree.Element("div")
  rdiv.append(subdoc)
  rdiv.append(DSetNode)

  return rdiv

def combine_references(DocNode, options):
  hrids = DocNode.xpath("//a[@class='hrid']")
  for hrid in hrids:
    subdocname = os.path.join(options.prefix, hrid.attrib["href"])
    subdoc = etree.parse(subdocname).getroot()
    subdoc = transform_hrid_table(subdoc, options)
    ipparent = hrid.getparent()
    ipparent.replace(hrid, subdoc)
  summarizes = DocNode.xpath("//a[@class='summarize']")
  for summarize in summarizes:
    subdocname = os.path.join(options.prefix, summarize.attrib["href"])
    subdoc = etree.parse(subdocname).getroot()
    subdoc = transform_summarize_table(subdoc, options)
    ipparent = summarize.getparent()
    ipparent.replace(summarize, subdoc)
  return DocNode

def retarget_resources(Node, options):
  ## we only support <img src='...' /> for now
  imgs = Node.xpath("//img")
  for img in imgs:
    src = img.get('src')
    src = os.path.join(options.prefix, src)
    img.set('src', src)
  return Node

def strip_width_from_tables(Node, options):
  ## strip the 'width' attribute from all tables
  ths = Node.xpath("//th[@width]")
  for th in ths:
    del th.attrib['width']
  return Node

def wrap_in_html(Node, options):
  wrapper = """<html xml:lang="en">
    <head>
      <title>Apathy Role Playing Game</title>
      <link rel="stylesheet" type="text/css"
            href="%s/Apathy.css" title="Apathy" />
    </head>
    <body>
      <combined-data-goes-here />
    </body>
  </html>
  """%options.prefix
  wrapnode = etree.fromstring(wrapper)
  cdgh = wrapnode.xpath("//combined-data-goes-here")[0]
  cdgh.getparent().insert(0, Node)
  html = wrapnode.xpath("//html")[0]
  html.set('xmlns', "http://www.w3.org/1999/xhtml")
  return wrapnode

def special_tag_transform(Node):
  apathys = Node.xpath("//Apathy")
  for apathy in apathys:
    apathy.tag = "span"
    apathy.set('class', "Apathy")
    txt = apathy.text
    apathy.text = "ApAthy"
    if txt is not None:
      apathy.text += txt
  return Node

def remove_by_timeperiod(Node, options):
  ## keeps only those time-periods before a certain date
  timeds = Node.xpath("//*[@timeperiod]")
  for timed in timeds:
    period = timed.get('timeperiod')
    if '*' == period:
      continue # good for any time period
    else:
      try: # get an integer!
        iperiod = int(period)
      except:
        continue # don't know what it is; invalid times are ignored
      if iperiod > options.time_period: # remove it
        timed.getparent().remove(timed)
  return Node

def remove_by_exclude_category(Node, options):
  catnodes = Node.xpath("//*[@category]")
  for catnode in catnodes:
    categories = catnode.get('category').split('|')
    for category in categories:
      if category in options.exclude:
        catnode.getparent().remove(catnode)
        break
  return Node

def report_categories(Node, options):
  if options.list_categories:
    catnodes = Node.xpath("//*[@category]")
    cats = []
    for catnode in catnodes:
      cats.append(catnode.get('category'))
    cats = list(set(cats))
    for cat in cats:
      print >> sys.stderr, cat

def insert_table_of_contents(Node):
  parts = Node.xpath("//div[@class='part']")
  partol = etree.Element("ol"); partol.set('class','toc')
  for part in parts:
    tocid = "toc-id%04d%04d"%(random.randint(1501,9995), random.randint(314,7505))
    part.set('id',tocid)
    partli = etree.SubElement(partol, 'li')
    parta = etree.SubElement(partli, 'a', href="#"+tocid)
    parta.text = part.xpath("./h1/p")[0].text
    chapters = part.xpath("descendant-or-self::div[@class='chapter']")
    chpol = etree.Element("ol"); chpol.set('class','toc')
    #print >> sys.stderr, " "*0+part.xpath("./h1/p")[0].text
    for chapter in chapters:
      chpid = "toc-id%04d%04d"%(random.randint(1501,9995), random.randint(314,7505))
      chapter.set('id',chpid)
      chpli = etree.SubElement(chpol, 'li')
      chpa = etree.SubElement(chpli, 'a', href="#"+chpid)
      chpa.text = chapter.xpath("./h1/p")[0].text
      sections = chapter.xpath("descendant-or-self::div[@class='section' and ../../@class='chapter']")
      secol = etree.Element('ol'); secol.set('class','toc')
      #print >> sys.stderr, " "*2+chapter.xpath("./h1/p")[0].text
      for section in sections:
        secid = "toc-id%04d%04d"%(random.randint(1501,9995), random.randint(314,7505))
        section.set('id', secid)
        secli = etree.SubElement(secol, 'li')
        seca = etree.SubElement(secli, 'a', href="#"+secid)
        seca.text = section.xpath("./h1/p")[0].text
        subsections = section.xpath("descendant-or-self::div[@class='section' and ../../../../@class='chapter']")
        #print >> sys.stderr, " "*4+section.xpath("./h1/p")[0].text
        subol = etree.Element('ol'); subol.set('class','toc')
        for subsection in subsections:
          subid = "toc-id%04d%04d"%(random.randint(1501,9995), random.randint(314,7505))
          subsection.set('id', subid)
          subli = etree.SubElement(subol, 'li')
          suba = etree.SubElement(subli, 'a', href="#"+subid)
          suba.text = subsection.xpath("./h1/p")[0].text
          #print >> sys.stderr, " "*6+subsection.xpath("./h1/p")[0].text
        if len(subsections) > 0:
          subsections[0].getparent().insert(0, subol)
      if len(sections) > 0:
        sections[0].getparent().insert(0, secol)
    if len(chapters) > 0:
      chapters[0].getparent().insert(0, chpol)
  # place partol before first part
  parts[0].getparent().insert(0, partol)
  return Node

def sanitize_string(string):
  return unicodeToLaTeX(string)

def convert_to_latex(Node, sectiondepth=0):
  latex = ""
  if Node.tag == 'div':
    if Node.attrib.has_key('class'):
      klass = Node.get('class')
      if klass == 'book':
        headerstr = convert_to_latex(Node.xpath("descendant::div[@class='header']")[0])+"\n\n\n"
        parts = Node.xpath("descendant-or-self::div[@class='part']")
        latex = ""
        for part in parts:
          latex += "\n"+convert_to_latex(part)
        return LATEX%(headerstr, latex)
      elif klass in 'part':
        title = Node.xpath("./h1/p")[0].text
        text = "\n\\part{%s}\n\n"%(sanitize_string(title))
        searcheds = Node.xpath("descendant::div[@class='chapter']")
        searchedstr = ""
        for searched in searcheds:
          searchedstr += convert_to_latex(searched)
        return text+searchedstr
      elif klass == 'chapter':
        title = Node.xpath("./h1/p")[0].text
        text = "\n\\chapter{%s}\n\n"%(sanitize_string(title))
        sections = Node.xpath("./div[@class='section-body']/*")
        sectstr = ""
        for section in sections:
          sectstr += convert_to_latex(section, 0)
        return text+sectstr
      elif klass == 'section':
        if sectiondepth > 2: sectiondepth = 2
        title = Node.xpath("./h1/p")[0].text
        text = "\n\\"+"sub"*sectiondepth+"section{"+"~"*sectiondepth+"%s}\n\n"%(sanitize_string(title))
        sections = Node.xpath("./div[@class='section-body']/div[@class='section']")
        sectstr = ""
        for section in sections:
          sectstr += convert_to_latex(section, sectiondepth+1)
        return text+sectstr
      elif klass == 'reference':
        print >> sys.stderr, "Academic reference..."
      elif klass == 'header':
        surround = "\\begin{center}\n\\vbox{\\small\n%s\n}\n\\end{center}\n\n"
        authors = Node.xpath("descendant::div[@class='author']")
        authstr = ""
        for author in authors:
          authstr += convert_to_latex(author)+"\\\\\n"
        return surround%authstr
      elif klass == 'author':
        children = Node.getchildren()
        if len(children) > 0:
          text = ""
          for child in children:
            text += convert_to_latex(child)
            return text
        else:
          return sanitize_string(Node.text)
      else:
        print >> sys.stderr, "Unknown div-class attribute `%s'."%klass
  elif Node.tag == 'img':
    imgtex = "\\includegraphics[width=1.00\\textwidth]{%s}"%(Node.get('src'))
    return imgtex
  else:
    print >> sys.stderr, "Unknown node named `%s'."%Node.tag
  return latex

def buildDocument(options):
  # combine together
  docname = os.path.join(options.prefix, options.main+".xhtml")
  maindoc = etree.parse(docname)
  maindoc = combine_references(maindoc, options)
  maindoc = special_tag_transform(maindoc)
  maindoc = retarget_resources(maindoc, options)
  report_categories(maindoc, options) 
  maindoc = remove_by_timeperiod(maindoc, options)
  maindoc = remove_by_exclude_category(maindoc, options)
  return maindoc

def buildLatex(options):
  maindoc = buildDocument(options)
  maindoc = convert_to_latex(maindoc.getroot())
  print >> sys.stdout, maindoc.encode("utf-8")

def buildWebPage(options):
  maindoc = buildDocument(options)
  maindoc = strip_width_from_tables(maindoc, options)
  maindoc = insert_table_of_contents(maindoc)
  maindoc = wrap_in_html(maindoc.getroot(), options)
  print >> sys.stdout, etree.tostring(maindoc)

if __name__=="__main__":
  options, args = parseOptions()

  if options.latex:
    buildLatex(options)
  if options.xhtml:
    buildWebPage(options)