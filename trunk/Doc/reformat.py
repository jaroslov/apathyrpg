import os, sys
from lxml import etree

files = os.listdir(".")

#for file in files:
#  if os.path.splitext(file)[-1] == ".xhtml":
#    cmd = "xmllint --format %s -o %s"%(file, file)
#    os.system(cmd)

for file in files:
  if os.path.splitext(file)[1] == ".xhtml":
    XML = etree.parse(file)
    print XML
