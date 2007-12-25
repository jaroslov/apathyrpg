#!/usr/bin/python

import sys, os
from xml.dom.minidom import parse as parseXml

#only inlines contain raw text
INLINES = ["text",
           "title",
           "define"]

FIX = ["field"]

def sanitize(apathy,inline,fix,indent=""):
  result = ""
  FixP = False
  if apathy.nodeType in [apathy.ELEMENT_NODE,
                         apathy.DOCUMENT_NODE]:    
    if apathy.tagName in inline:
      return indent + apathy.toxml()+"\n"
    if apathy.tagName in fix:
      FixP = True
    result += indent+"<"+apathy.tagName
    keys = apathy.attributes.keys()
    keys.sort()
    for key in keys:
      value = apathy.attributes[key].nodeValue
      value = value.replace('"',"&quot;")
      value = value.replace("'","&apos;")
      result += " "+key+'="'+value+'"'
  hasChildren = False
  childRes = ""
  if FixP:
    Problem = False
    for child in apathy.childNodes:
      hasChildren = True
      childRes += child.toxml()
      if child.nodeType == child.ELEMENT_NODE:
        if child.tagName in ["text",
                             "description-list",
                             "enumerated-list",
                             "itemized-list",
                             "example",
                             "note"]:
          Problem = True
    if Problem:
      pass#print >> sys.stderr, apathy.toxml()
  else:
    for child in apathy.childNodes:
      hasChildren = True
      childRes += sanitize(child,inline,fix,indent+"  ")
  if apathy.nodeType in [apathy.ELEMENT_NODE,
                         apathy.DOCUMENT_NODE]:
    if not hasChildren:
      result += "/>\n"
    elif FixP:
      result += "><text>"+childRes+"</text></"+apathy.tagName+">\n"
    else:
      result += ">\n"+childRes+indent+"</"+apathy.tagName+">\n"
  return result

if __name__=="__main__":
  apathy = parseXml("../Apathy.xml")
  result = sanitize(apathy.childNodes[0],INLINES,FIX)
  print result