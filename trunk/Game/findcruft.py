import sys
import xml.dom.minidom as minix

def findcruft (Where, xml=None, NodeKinds=None):
  if Where is not None:
    NodeKinds = {}
    xml = minix.parse(Where)
    findcruft (None, xml, NodeKinds)
  else:
    if xml.nodeName not in NodeKinds:
      NodeKinds[xml.nodeName] = 1
    else:
      NodeKinds[xml.nodeName] += 1
    if xml.hasChildNodes:
      for child in xml.childNodes:
        findcruft (None, child, NodeKinds)
  return NodeKinds

def sanitizews (string):
  for rep in [" ","\t","\v","\f","\n"]:
    string = string.replace(rep," ")
  for idx in xrange(10):
    string = string.replace("  "," ")
  return string.strip()

def selfsimilarstrings (Where, xml=None, Strings={}):
  if Where is not None:
    xml = minix.parse (Where)
    selfsimilarstrings (None, xml, Strings)
  else:
    if xml.nodeType == xml.TEXT_NODE:
      stext = sanitizews (xml.nodeValue)
      words = stext.split (" ")
      for word in words:
        if word not in Strings:
          Strings[word] = 1
        else:
          Strings[word] += 1
    if xml.hasChildNodes ():
      for child in xml.childNodes:
        selfsimilarstrings (None, child, Strings)
  return Strings

def pseudoprettyprint (Where, xml=None, indent="", inlines=[]):
  result = u""
  if Where is not None:
    xml = minix.parse(Where)
    result += pseudoprettyprint (None, xml, inlines=inlines)
    result = result.replace (u'\u2018',"&lsquo;").replace (u'\u2019',"&rsquo;")
    result = result.replace (u'\u201c',"&ldquo;").replace (u'\u201d',"&rdquo;")
    result = result.replace (u'\xd7', "&times;").replace (u'\u03a3', "&Sigma;")
    result = result.replace (u'\u2192', "&rarr;").replace (u'\xae', "&reg;")
  else:
    if xml.nodeType == xml.ELEMENT_NODE:
      if xml.nodeName not in inlines:
        result += indent
      result += "<"+xml.nodeName
      if xml.attributes:
        for attr in xml.attributes.keys ():
          result += u" "+attr+"='"+xml.getAttribute (attr)+"'"
      if xml.hasChildNodes ():
        result += u">"
        once = True
        for child in xml.childNodes:
          if (child.nodeType == xml.ELEMENT_NODE
            and once and child.nodeName not in inlines):
            result += "\n"+indent
            once = False
          result += pseudoprettyprint (None, child, "  "+indent, inlines=inlines)
          if (child.nodeType == xml.ELEMENT_NODE
            and child.nodeName not in inlines):
            result += "\n"+indent
        result += "</"+xml.nodeName+">"
      else:
        result += "/>"
    elif xml.nodeType == xml.TEXT_NODE:
      result += u""+sanitizews(xml.nodeValue)
    else:
      if xml.hasChildNodes ():
        for child in xml.childNodes:
          result += pseudoprettyprint (None, child, "", inlines=inlines)
  return result

if __name__=="__main__":
  where = "Apathy.xml"
  which = "findcruft"
  inlines = []
  if len(sys.argv) > 1:
    where = sys.argv[1]
  if len(sys.argv) > 2:
    which = sys.argv[2]
  if len(sys.argv) > 3:
    cfg = open(sys.argv[3],"r")
    cfglines = cfg.readlines ()
    for line in cfglines:
      if "inlines:" in line:
        inlines = line[len("inlines:"):].split(",")
        for idx in xrange(len(inlines)):
          inlines[idx] = inlines[idx].strip()
    print "Inlines:", inlines
  if "findcruft" == which:
    cruft = findcruft (where)
    ckeys = cruft.keys ()
    ckeys.sort ()
    for ckey in ckeys:
      print ckey+":", cruft[ckey]
  elif "pseudopp" == which:
    result = pseudoprettyprint (where, inlines=inlines)
    beauty = open ("beautified.xml","w")
    print >> beauty, result
  elif "selfsim" == which:
    strings = selfsimilarstrings (where)
    skeys = strings.keys ()
    skeys.sort ()
    for skey in skeys:
      print "("+skey+": "+str(strings[skey])+")"
    print