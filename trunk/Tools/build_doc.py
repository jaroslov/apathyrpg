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
    <p/>
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
    bodyp = DescNode.xpath("//div[@class='description-body']/p")[0]
    titlep.text = row.getchildren()[titledx].xpath("p")[0].text
    Nid = "id%s"%(apathy_hash(titlep.text))
    bodyp.text = row.getchildren()[descdx].xpath("p")[0].text
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

def buildLatex(options): pass

def buildWebPage(options):
  # combine together
  docname = os.path.join(options.prefix, options.main+".xhtml")
  maindoc = etree.parse(docname)
  maindoc = combine_references(maindoc, options)
  maindoc = special_tag_transform(maindoc)
  maindoc = wrap_in_html(maindoc.getroot(), options)
  print >> sys.stdout, etree.tostring(maindoc)

if __name__=="__main__":
  options, args = parseOptions()

  if options.latex:
    buildLatex(options)
  if options.xhtml:
    buildWebPage(options)
