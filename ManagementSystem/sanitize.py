#!/usr/bin/python

import sys, os
from xml.dom.minidom import parse as parseXml

def sanitize(apathy,id_init=[0]):
  for child in apathy.childNodes:
    if child.nodeType == child.ELEMENT_NODE:
      print "{{".child.tagName
    elif child.nodeType == child.TEXT_NODE:
      print child.nodeValue

if __name__=="__main__":
  apathy = parseXml("../Apathy.xml")
  sanitize(apathy.childNodes[0])
  local = open("Apathy.id.xml","w")
  print >> local, apathy.toxml()
  #apathy.toxml(local)