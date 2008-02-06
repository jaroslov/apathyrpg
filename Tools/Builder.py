#!/usr/bin/env python2.5

import os, sys
from optparse import OptionParser
from xml.dom.minidom import parse as parseXml

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
                    action="store_true",help="attempt to the clean the source files")
  parser.add_option("","--main-document",dest="main",
                    help="the name of the main document, defaults to 'Apathy'",
                    metavar="FILE")
  
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
  whom= os.path.join(options.prefix, who)
  if suffix: whom += ".xhtml"
  return parseXml(whom)

def getReferenceSet(XML):
  """
  Retrieves a node-set containing all the "references"
  in a document; "references" are of this form:
  <a class="hrid" href="...">...</a>
  """
  anchors = XML.getElementsByTagName("a")
  references = []
  for anchor in anchors:
    if not anchor.hasAttribute("class"):
      continue
    if anchor.getAttribute("class") != "hrid":
      continue
    if not anchor.hasAttribute("href"):
      continue
    references.append(anchor)
  return references

def nullTranslator(XML):
  return [XML]

def __combine(options, translate, report=sys.stdout):
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
    newChildren = translate(root.cloneNode(root))
    for newChild in newChildren:
      parent.insertBefore(root.cloneNode(root), reference)
    parent.removeChild(reference)
  return Main

def combine(options):
  """
  Simplest way to combine data and save; example code.
  """
  combined = __combine(options, nullTranslator)
  writeToDisk(combined,".combine.xhtml")

def tableAsWebTable(XML):
  """
  Takes tabular xhtml documents and converts them into web-form
  We are given the "table", so double-check
  """
  if XML.nodeType != XML.ELEMENT_NODE:
    return XML # fail nicely
  if XML.tagName.lower() != "table":
    return XML # fail nicely
  if (not XML.hasAttribute("class")
      and XML.getAttribute("class").lower() != "category"):
    return XML # fail nicely
  theads = XML.getElementsByTagName("thead")
  ## FINISH HERE
  #  it is fun
  titles = None
  display = None
  for thead in theads:
    if thead.hasAttribute("class"):
      cls = thead.getAttribute("class")
      if cls == "titles": titles = thead
      if cls == "display": display = thead
  displayParent = display.parentNode
  titlesParent = titles.parentNode
  displayC = display.cloneNode(display)
  titlesC = titles.cloneNode(titles)

  # build the description sections, which are a list of DIV;
  # also, find the actual title
  displayKind = []
  displayKindMap = {}
  for ddx in xrange(len(displayC.childNodes)):
    disp = displayC.childNodes[ddx]
    kind = disp.firstChild.nodeValue.lower()
    displayKind.append(kind)
    if displayKindMap.has_key(kind): displayKindMap[kind].append(ddx)
    else: displayKindMap[kind] = [ddx]

  titleLoc = -1
  for ddx in xrange(len(titlesC.childNodes)):
    ttl = titlesC.childNodes[ddx]
    if ttl.firstChild.nodeValue.lower() == "name":
      titleLoc = ddx

  descriptions = []
  # rowset of all tr within the table
  rowset = XML.getElementsByTagName("tr")
  for row in rowset:
    # duplicate the row
    # throw away everything but title and description
    # rename row to "div", add "@class";
    # rename title td to "h1" and body "td" to "div"
    # add appropriate attributes
    div = row.cloneNode(row)
    removes = []
    for tdx in xrange(len(div.childNodes)):
      td = div.childNodes[tdx]
      if tdx != titleLoc and tdx not in displayKindMap["description"]:
        removes.append(div.childNodes[tdx])
      elif tdx == titleLoc:
        td.tagName = "h1"
        td.setAttribute("class","title")
      elif tdx in displayKindMap["description"]:
        td.tagName = "div"
        td.setAttribute("class","description-body")
    for remove in removes:
      div.removeChild(remove)
    div.tagName = "div"
    div.setAttribute("class","description")
    descriptions.append(div)
    # now, go through the main table and remove all non-table entries
    for td in row.childNodes:
      print td.nodeValue

  return [XML,descriptions]

def addToc(XML, intersperse=True):
  """
  Builds tables-of-contents (interspersed or not) into the webpage.
  Interspersed: places small TOCs at each structural level
  Not interspersed: places one global TOC at the top-level
  """
  return XML

def buildWebPage(options):
  combined = __combine(options, tableAsWebTable, report=sys.stderr)
  combined = addToc(combined)
  writeToDisk(combined, ".webpage.xhtml")

def writeToDisk(XML, appendix):
  """
  Writes the XML to disk with the correct encoding.
  """
  output_name = options.output+appendix
  target = open(output_name,"w")
  print >> target, XML.toxml(encoding="utf-8")

if __name__=="__main__":
  options, args = parseOptions()

  if options.latex:
    buildLatex(options)
  if options.xhtml:
    buildWebPage(options)
  if options.combine:
    combine(options)
    