import os, sys
from lxml import etree

files = os.listdir(".")

#for file in files:
#  if os.path.splitext(file)[-1] == ".xhtml":
#    cmd = "xmllint --format %s -o %s"%(file, file)
#    os.system(cmd)

NS = "{http://www.w3.org/1999/xhtml}"

for file in files:
  if os.path.splitext(file)[1] == ".xhtml":
    XML = etree.parse(file).getroot()
    if XML.tag == "table":
      theads = { "titles"  : XML.xpath("//thead[@name='titles']")[0],
                 "display" : XML.xpath("//thead[@name='display']")[0],
                 "format"  : XML.xpath("//thead[@name='format']")[0]}
      thparent = theads["titles"].getparent()
      ths = {}
      for key, value in theads.items():
        ths[key] = XML.xpath("//thead[@name='%s']/th"%key)
      for thdx in xrange(len(ths["titles"])):
        ths["titles"][thdx].set('width', str(ths["format"][thdx].text))
        ths["titles"][thdx].set('class', str(ths["display"][thdx].text))
      thparent.remove(theads["display"])
      thparent.remove(theads["format"])
      value = etree.tostring(XML)
      ofile = open(file, "w")
      print >> ofile, value
