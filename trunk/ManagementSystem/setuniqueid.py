#!/usr/bin/python

import sys, os
from xml.dom.minidom import parse as parseXml

def setUniqueID(apathy,id_init=[0]):
  for child in apathy.childNodes:
    if child.nodeType == child.ELEMENT_NODE:
      if child.attributes.has_key("id"):
        del child.attributes["id"];
      if not child.attributes.has_key("xml:id"):
        id_init[0] += 1
        child.attributes["xml:id"] = ""
      child.setAttribute("xml:id",("G%7s"%str(id_init[0])).replace(" ","0"))
      setUniqueID(child)

if __name__=="__main__":
  apathy = parseXml("../Apathy.xml")
  setUniqueID(apathy.childNodes[0])
  local = open("Apathy.id.xml","w")
  print >> local, apathy.toxml()
  #apathy.toxml(local)