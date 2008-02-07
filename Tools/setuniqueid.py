#!/usr/bin/python

import sys, os
from xml.dom.minidom import parse as parseXml

def setUniqueID(apathy,id_init=[0],addOrRemove=True):
  for child in apathy.childNodes:
    if child.nodeType == child.ELEMENT_NODE:
      id_init[0] += 1
      if not addOrRemove:
        if child.attributes.has_key("xml:id"):
          del child.attributes["xml:id"];
      else:
        if not child.attributes.has_key("xml:id"):
          child.attributes["xml:id"] = ""
        child.setAttribute("xml:id",("G%7s"%str(id_init[0])).replace(" ","0"))
      setUniqueID(child,id_init,addOrRemove)

if __name__=="__main__":
  name = "Apathy.xml"
  if len(sys.argv) > 1:
    name = sys.argv[1]
  apathy = parseXml(name)
  setUniqueID(apathy.childNodes[0],[0],False)
  local = open("Apathy.id.xml","w")
  print >> local, apathy.toxml()
  #apathy.toxml(local)