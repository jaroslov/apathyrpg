#!/usr/bin/python

from xml.dom.minidom import parse as parseXml

apathy = parseXml("Apathy.xml")

def recurseDom(XML,indent=""):
  try:
    for child in XML.childNodes:
      print indent+str(child)
      recurseDom(child,indent+" ")
  except:
    pass

recurseDom(apathy)