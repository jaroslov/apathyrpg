#!/usr/bin/env python2.5

import os, sys
from optparse import OptionParser
from xml.dom.minidom import parse as parseXml
from random import SystemRandom

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
      parent.insertBefore(newChild, reference)
    parent.removeChild(reference)
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
    self.TableOnly = TableOnly
  def __call__(self, XML):
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
    if self.TableOnly:
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

def htmlToLatex(XML):
  """
    Converts (X)HTML nodes into their appropriate LaTeX version.
    
  """
  result = ""
  if XML.nodeType == XML.ELEMENT_NODE:
    if XML.tagName == "div":
      cls = None
      if XML.hasAttribute("class"): cls = XML.getAttribute("class")
      if cls == "book":
        result += "\documentclass{book}\n"
  return result

def buildLatex(options):
  combined = __combine(options, tableAsWebTable(), report=sys.stderr)
  combined = htmlToLatex(combined)
  writeToDisk(options, combined, ".tex")

def buildWebPage(options):
  combined = __combine(options, tableAsWebTable(), report=sys.stderr)
  combined = addToc(combined)
  combined = wrapInHtml(options, combined, options.output)
  writeToDisk(options, combined, ".webpage.xhtml")

def writeToDisk(options, XML, appendix):
  """
  Writes the XML to disk with the correct encoding.
  """
  output_name = options.output+appendix
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
  if options.combine:
    combine(options)
    