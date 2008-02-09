#!/usr/bin/env python2.5

import os, sys
from optparse import OptionParser
from xml.dom.minidom import parse as parseXml
from random import SystemRandom
import codecs
from Uni2LaTeX import unicodeToLaTeX

def parseOptions():
  parser = OptionParser()
  parser.add_option("-p","--prefix",dest="prefix",
                    help="[required] the directory of the document",
                    metavar="FOLDER")
  parser.add_option("-o","--output",dest="output",
                    help="the name of the output file",
                    metavar="FILE")
  parser.add_option("-c","--combine",dest="combine",
                    action="store_true",help="produce a single source file")
  parser.add_option("-l","--latex",dest="latex",
                    action="store_true",help="produce LaTeX output")
  parser.add_option("-w","--xhtml",dest="xhtml",
                    action="store_true",help="produce webpage output")
  parser.add_option("","--clean",dest="clean",
                    action="store_true",
                    help="attempt to the clean the source files")
  parser.add_option("","--main-document",dest="main",
                    help="the name of the main document, defaults to 'Apathy'",
                    metavar="FILE")
  parser.add_option("","--pretty-print",dest="prettyprint",
                    help="pretty-print the resulting file",action="store_true")
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
  if options.combine is None:
    options.combine = False
  if options.clean is None:
    options.clean = False
  if options.main is None:
    options.main = "Apathy"
  if options.prettyprint is None:
    options.prettyprint = False
  if options.retargetresources is None:
    options.retargetresources = False

  return options, args

def getAllXhtmls(options):
  """
  Retrieves all the files in a directory whose name ends with '.xhtml'
  """
  files = os.listdir(options.prefix)
  xhtmls = {}
  for file in files:
    extension = os.path.splitext(file)
    if extension[-1] not in [".xhtml"]:
      continue
    real_name = os.path.split(extension[0])[-1]
    actual_path = os.path.join(options.prefix, file)
    xhtmls[real_name] = parseXml(actual_path)
  return xhtmls

def getMainXhtml(options):
  """
  Retrieves only the main xhtml file
  """
  return getXhtmlDocument(options, options.main, True)

def getXhtmlDocument(options, who, suffix=False):
  """
  Retrieves some xhtml file from the xhtml set
  """
  whom = os.path.join(options.prefix, who)
  if suffix: whom += ".xhtml"
  if not os.path.isfile(whom) and not suffix:
    # try appending "xhtml" anyways
    whom += ".xhtml"
    if not os.path.isfile(whom): # give up
      raise Exception, "I don't know: "+who
  return parseXml(whom)

def getSumRefSet(XML,Class):
  anchors = XML.getElementsByTagName("a")
  references = []
  for anchor in anchors:
    if not anchor.hasAttribute("class"):
      continue
    if anchor.getAttribute("class") != Class:
      continue
    if not anchor.hasAttribute("href"):
      continue
    references.append(anchor)
  return references

def getReferenceSet(XML):
  """
  Retrieves a node-set containing all the "references"
  in a document; "references" are of this form:
  <a class="hrid" href="...">...</a>
  """
  return getSumRefSet(XML,"hrid")

def getSummarySet(XML):
  """
  """
  return getSumRefSet(XML,"summarize")

def nullTranslator(XML,Kind):
  return [XML]

def __seperate(options, translate, report=sys.stdout):
  Main = getMainXhtml(options)
  res = {options.output:Main}
  references = getReferenceSet(Main)
  for reference in references:
    which = reference.getAttribute("href")
    xhtml = getXhtmlDocument(options, which)
    root = None
    for child in xhtml.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        root = child
        break
    print >> report, which
    key = "Reference--"+which
    reference.setAttribute("href",key)
    translation = translate(root.cloneNode(root),"reference")
    translated = catXHTML(translation)
    res[key] = translated
  references = getSummarySet(Main)
  for reference in references:
    which = reference.getAttribute("href")
    xhtml = getXhtmlDocument(options, which)
    root = None
    for child in xhtml.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        root = child
        break
    print >> report, which
    key = "Summary--"+which
    reference.setAttribute("href",key)
    translation = translate(root.cloneNode(root),"summary")
    translated = catXHTML(translation)
    res[key] = translated
  return res

def __combine(options, translate, report=sys.stdout, fastHack=False):
  Main = getMainXhtml(options)
  references = getReferenceSet(Main)
  for reference in references:
    which = reference.getAttribute("href")
    xhtml = getXhtmlDocument(options, which)
    parent = reference.parentNode
    # grab first ELEMENT_NODE from the document
    # it is necessarily the root
    root = None
    for child in xhtml.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        root = child
        break
    print >> report, which
    # get the new children and insert before the reference,
    # then remove the reference, itself
    newChildren = translate(root.cloneNode(root),"reference")
    for newChild in newChildren:
      parent.insertBefore(newChild, reference)
    parent.removeChild(reference)
    if fastHack: break
  # change all summaries into tabular-forms
  summaries = getSummarySet(Main)
  for summary in summaries:
    which = summary.getAttribute("href")
    xhtml = getXhtmlDocument(options, which)
    parent = summary.parentNode
    # get first ELEMENT_NODE from the document
    root = None
    for child in xhtml.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        root = child
        break
    print >> report, which
    newChildren = translate(root.cloneNode(root),"summary")
    for newChild in newChildren:
      parent.insertBefore(newChild, summary)
      parent.removeChild(summary)
    if fastHack: break
  # change all the Apathy tags to span tags
  Apathys = Main.getElementsByTagName("Apathy")
  for Apathy in Apathys:
    Apathy.setAttribute("class","Apathy")
    Apathy.tagName = "span"
    apathytext = Main.createTextNode("Apathy")
    if Apathy.hasChildNodes():
      Apathy.insertBefore(apathytext,Apathy.firstChild)
    else:
      Apathy.appendChild(apathytext)
  if options.retargetresources:
    imgs = Main.getElementsByTagName("img")
    for img in imgs:
      npath = os.path.join(options.prefix,img.getAttribute("src"))
      npath = os.path.normpath(npath)
      img.setAttribute("src",npath)
  return Main

def combine(options):
  """
  Simplest way to combine data and save; example code.
  """
  combined = __combine(options, nullTranslator)
  writeToDisk(options, combined,".combine.xhtml")

class tableAsWebTable(object):
  def __init__(self, DoAnchors=True, TableOnly=False):
    self.DoAnchors = DoAnchors
  def __call__(self, XML, Kind):
    """
    Takes tabular xhtml documents and converts them into web-form
    We are given the "table", so double-check
    """
    SR = SystemRandom()
    if XML.nodeType != XML.ELEMENT_NODE:
      return XML # fail nicely
    if XML.tagName.lower() != "table":
      return XML # fail nicely
    if (not XML.hasAttribute("class")
        and XML.getAttribute("class").lower() != "category"):
      return XML # fail nicely
    theads = XML.getElementsByTagName("thead")
    titles = None
    display = None
    format = None
    for thead in theads:
      if thead.hasAttribute("class"):
        cls = thead.getAttribute("class")
        if cls == "titles": titles = thead
        if cls == "display": display = thead
        if cls == "format": format = thead
    if titles is None or display is None or format is None:
      return [XML]
    displayParent = display.parentNode
    titlesParent = titles.parentNode
    formatParent = format.parentNode
    displayC = display.cloneNode(display)
    titlesC = titles.cloneNode(titles)
    formatC = format.cloneNode(format)
  
    # build the description sections, which are a list of DIV;
    # also, find the actual title
    displayKind = []
    displayKindMap = {}
    displayKindMap["table"] = [] # simplifies things later
    displayKindMap["name"] = []
    for ddx in xrange(len(displayC.childNodes)):
      disp = displayC.childNodes[ddx]
      kind = disp.firstChild.nodeValue.lower()
      displayKind.append(kind)
      if displayKindMap.has_key(kind): displayKindMap[kind].append(ddx)
      else: displayKindMap[kind] = [ddx]
  
    displayParent.removeChild(display)
    formatParent.removeChild(format)
  
    titleLoc = -1
    for ddx in xrange(len(titlesC.childNodes)):
      ttl = titlesC.childNodes[ddx]
      if ttl.firstChild.nodeValue.lower() == "name":
        titleLoc = ddx
  
    descriptions = []
    # rowset of all tr within the table
    rowset = XML.getElementsByTagName("tr")
    for tr in rowset:
      # duplicate the row
      # throw away everything but title and description
      # rename row to "div", add "@class";
      # rename title td to "h1" and body "td" to "div"
      # add appropriate attributes
      div = tr.cloneNode(tr)
      removes = []
      GID = "G"+str(SR.randrange(5001, 214000000))
      for tdx in xrange(len(div.childNodes)):
        td = div.childNodes[tdx]
        if tdx != titleLoc and tdx not in displayKindMap["description"]:
          removes.append(div.childNodes[tdx])
        elif tdx == titleLoc:
          td.tagName = "h1"
          td.setAttribute("class","description-title")
        elif tdx in displayKindMap["description"]:
          td.tagName = "div"
          td.setAttribute("class","description-body")
      for remove in removes:
        div.removeChild(remove)
      div.tagName = "div"
      div.setAttribute("class","description")
      div.setAttribute("id",GID)
      descriptions.append(div)
  
      # now, go through the main table and remove all non-table entries
      removes = []
      for tdx in xrange(len(tr.childNodes)):
        td = tr.childNodes[tdx]
        if tdx != titleLoc and tdx not in displayKindMap["table"]:
          removes.append(td)
        if tdx == titleLoc and self.DoAnchors:
          #anchor = td.cloneNode(td)
          #anchor.tagName = "a"
          #anchor.setAttribute("href","#"+GID)
          #td.childNodes = []
          #td.appendChild(anchor)
          if td.hasChildNodes():
            p = None
            for child in td.childNodes:
              if (child.nodeType == child.ELEMENT_NODE
                  and child.tagName.lower() == "p"):
                child.tagName = "a"
                child.setAttribute("href","#"+GID)
                break
      for remove in removes:
        tr.removeChild(remove)
  
    removes = []
    for thx in xrange(len(titles.childNodes)):
      th = titles.childNodes[thx]
      if thx != titleLoc and thx not in displayKindMap["table"]:
        removes.append(th)
    for remove in removes:
      titles.removeChild(remove)
  
    descriptions.insert(0, XML)
    if Kind == "summary":
      return [XML]
    return descriptions

def getSubstructureElements(Node):
  """
  Retrieves any node called "part", "chapter", "section"
  within the section-body of this node
  """
  substructure = []
  sectionbody = None
  for child in Node.childNodes:
    if (child.nodeType == child.ELEMENT_NODE
        and child.tagName == "div"
        and child.hasAttribute("class")
        and child.getAttribute("class") in ["section-body"]):
      sectionbody = child
      break
  if sectionbody is None:
    return [] # fail silently
  for child in sectionbody.childNodes:
    if (child.nodeType == child.ELEMENT_NODE
        and child.tagName == "div"
        and child.hasAttribute("class")
        and child.getAttribute("class") in ["part","chapter","section"]):
      substructure.append(child)
  return substructure

def createLocalToc(StructureNode):
  SR = SystemRandom()
  substructure = getSubstructureElements(StructureNode)
  document = StructureNode.ownerDocument
  ol = document.createElement("ol")
  ol.setAttribute("class","toc")
  secbody = None
  for subs in substructure:
    GID = "G"+str(SR.randrange(5001, 214000000))
    secbody = subs.parentNode
    title = None
    for child in subs.childNodes:
      if (child.nodeType == child.ELEMENT_NODE and child.tagName=="h1"
          and child.hasAttribute("class")
          and child.getAttribute("class") == "title"):
        title = child
        title.setAttribute("id",GID)
    if title is None or not title.hasChildNodes():
      continue # fail silently
    ps = title.getElementsByTagName("p")
    if len(ps) == 0:
      continue # fail silently
    anchor = ps[0].cloneNode(ps[0])
    anchor.tagName = "a"
    anchor.setAttribute("href","#"+GID)
    li = document.createElement("li")
    li.appendChild(anchor)
    ol.appendChild(li)
  return ol

def addLocalToc(Node):
  if (Node.nodeType == Node.ELEMENT_NODE and Node.tagName == "div"
      and Node.hasAttribute("class")
      and Node.getAttribute("class") in ["book","part","chapter","section"]):
      secbody = None
      for child in Node.childNodes:
        if (child.nodeType == child.ELEMENT_NODE and child.tagName == "div"
            and child.hasAttribute("class")
            and child.getAttribute("class") == "section-body"):
          secbody = child
          break
      ol = createLocalToc(Node)
      if secbody is not None: # work quietly
        secbody.insertBefore(ol, secbody.firstChild)
  for child in Node.childNodes:
    addLocalToc(child)
  return Node

def addToc(XML):
  """
  Builds tables-of-contents (interspersed) into the webpage.
  Interspersed: places small TOCs at each structural level
  """
  addLocalToc(XML)
  return XML

def wrapInHtml(options,XML,Title):
  """
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
      <title><Title /></title>
      <link rel="stylesheet" type="text/css"
            href="Apathy.css" title="Apathy" />
    </head>
    <body>
      <combined-data-goes-here />
    </body>
  </html>
  """
  html = XML.createElement("html")
  html.setAttribute("xmlns","http://www.w3.org/1999/xhtml")
  html.setAttribute("xml:lang","en")
  head = XML.createElement("head")
  title = XML.createElement("title")
  titletext = XML.createTextNode(Title)
  title.appendChild(titletext)
  link = XML.createElement("link")
  link.setAttribute("rel","stylesheet")
  link.setAttribute("type","text/css")
  linkref = "Apathy.css"
  if options.retargetresources:
    linkref = os.path.normpath(os.path.join(options.prefix,linkref))
  link.setAttribute("href",linkref)
  link.setAttribute("title","Apathy")
  head.appendChild(title)
  head.appendChild(link)
  body = XML.createElement("body")
  for child in XML.childNodes:
    if child.nodeType == child.ELEMENT_NODE:
      body.appendChild(child)
  html.appendChild(head)
  html.appendChild(body)
  return html

def catXHTML(XMLs):
  if len(XMLs) == 0:
    raise Exception, "Empty set of XHTML fragments"
  document = XMLs[0].ownerDocument
  div = document.createElement("div")
  div.setAttribute("class","main")
  for XML in XMLs:
    div.appendChild(XML)
  doc = document.cloneNode(document)
  doc.childNodes = []
  doc.appendChild(div)
  return doc

def htmlToLaTeXC(XML):
  result = ""
  for child in XML.childNodes:
    result += htmlToLaTeX(child)
  return result

def htmlToLaTeX(XML):
  """
    Converts (X)HTML nodes into their appropriate LaTeX version.
  """
  result = ""
  if XML.nodeType == XML.DOCUMENT_NODE:
    result += """\documentclass[twoside]{book}
\usepackage{multicol}
\usepackage{rotating}
\usepackage{tabularx}
\usepackage{array}
\usepackage{longtable}
\usepackage{multirow}
\usepackage{graphicx}
\usepackage[T1]{fontenc}
\usepackage{hyperref}
\usepackage{wrapfig}
\usepackage[text={6.5in,8in},textheight=8in]{geometry}\n"""
    result += htmlToLaTeXC(XML)
  elif XML.nodeType == XML.ELEMENT_NODE:
    tagl = XML.tagName.lower()
    if tagl == "div":
      cls = None
      if XML.hasAttribute("class"): cls = XML.getAttribute("class")
      if cls == "book": # the whole book
        result += "\\begin{document}\n"
        result += htmlToLaTeXC(XML)
        result += "\\end{document}"
      elif cls == "header": # title page
        result += "\\begin{titlepage}\n\\begin{center}"
        result += htmlToLaTeXC(XML)
        result += "\\end{center}\n\\end{titlepage}\n"
        result += """\\setcounter{page}{1}
\\pagenumbering{roman}
\\setcounter{tocdepth}{3}
\\tableofcontents
\\newpage
\\listoftables
\\newpage
\\listoffigures
\\newpage
\\pagenumbering{arabic}
\\setcounter{page}{1}\n"""
      elif cls == "authors":
        result += "\\vbox{\\small\n"
        result += htmlToLaTeXC(XML)
        result += "}\n"
      elif cls == "author":
        result += htmlToLaTeXC(XML)+"\\\\\n"
      elif cls == "section-body":
        result += htmlToLaTeXC(XML)
      elif cls in ["part","chapter","section"]:
        title = None
        body = None
        for child in XML.childNodes:
          if child.nodeType == child.ELEMENT_NODE:
            if child.tagName == "h1":
              title = child
            elif child.tagName == "div":
              body = child
        result += "\\%s{"%(cls)+htmlToLaTeX(title)+"}\n"
        result += htmlToLaTeX(body)
      else:
        print cls,
    elif tagl == "span":
      cls = None
      if XML.hasAttribute("class"):
        cls = XML.getAttribute("class")
      if cls == "Apathy":
        result += "{\\bf "+htmlToLaTeXC(XML)+"}"
    elif tagl == "h1":
      result += htmlToLaTeXC(XML)
    elif tagl == "p":
      result += "{"+htmlToLaTeXC(XML)+"}"
    elif tagl == "img":
      img = "\\includegraphics[width=%s\\textwidth]{%s}"
      width = "1.0"
      if XML.hasAttribute("width"):
        wd = XML.getAttribute("width")
        if "%" in wd:
          wd = wd.replace("%","")
        wd = float(wd)
        width = "%3.2f"%(wd / 100)
      src = XML.getAttribute("src")
      if options.retargetresources:
        #src = os.path.join(options.prefix,src) ### BUG! Doc/Doc/...
        src = os.path.normpath(src)
      img = img%(width, src)
      result += img
    else:
      print XML.tagName,
  elif XML.nodeType == XML.TEXT_NODE:
    result += unicodeToLaTeX(XML.nodeValue)
  return result

def buildLatex(options):
  combined = __combine(options, tableAsWebTable(), report=sys.stderr, fastHack=True)
  LaTeX = htmlToLaTeX(combined)
  target = open(options.output+".combine.tex","w")
  print >> target, LaTeX.encode("utf-8")

def buildWebPage(options):
  if options.combine:
    page = __combine(options, tableAsWebTable(), report=sys.stderr)
    page = addToc(page)
    page = wrapInHtml(options, page, options.output)
    writeToDisk(options, page, ".webpage.xhtml")
  else:
    pages = __seperate(options, tableAsWebTable(), report=sys.stderr)
    for key,page in pages.items():
      page = wrapInHtml(options, page, key)
      page = addToc(page)
      keyname = key.replace(": ","--")
      appendix = ""
      if ".xhtml" not in keyname:
        appendix = ".xhtml"
      writeToDisk(options, page, appendix=appendix, altname=keyname)

def writeToDisk(options, XML, appendix, altname=None):
  """
  Writes the XML to disk with the correct encoding.
  """
  if altname is None:
    output_name = options.output+appendix
  else:
    output_name = altname+appendix
  target = open(output_name,"w")
  # pretty printing is worse than regular printing, by far
  if options.prettyprint:
    print >> target, XML.toxml(encoding="utf-8")
  else:
    print >> target, XML.toxml(encoding="utf-8")

if __name__=="__main__":
  options, args = parseOptions()

  if options.latex:
    buildLatex(options)
  if options.xhtml:
    buildWebPage(options)    