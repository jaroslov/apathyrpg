#!/usr/bin/env pythonw

import os, sys#, libxslt, libxml2
from optparse import OptionParser

def parseOptions():
  parser = OptionParser()
  parser.add_option("-o","--output-file",dest="output_file",
          help="the output file name",metavar="FILE")

  parser.add_option("-x","--xml-to-xhtml",dest="x2h",
          help="force conversion xml to xhtml",action="store_true")

  parser.add_option("-t","--xhtml-to-xml",dest="x2h",
          help="force conversion xhtml to xml",action="store_false")

  parser.add_option("-s","--alt-xslt",dest="alt_xslt",
          help="an alternate xslt to the default",metavar="FILE")

  parser.usage += " File"

  (options, args) = parser.parse_args()

  # args is ONE file
  if len(args) != 1:
    parser.print_help()
    sys.exit(1)

  # automatically determine transfer direction
  if options.x2h is None:
    suffix = os.path.splitext(args[0])
    options.x2h = (".xml"==suffix[1])

  # automatic generation of output file name
  if options.output_file is None:
    suffix = os.path.splitext(args[0])
    if options.x2h: suffix = (suffix[0],".xhtml")
    else: suffix = (suffix[0],".xml")
    options.output_file = suffix[0]+suffix[1]

  return options, args[0]

if __name__=="__main__":
  (options, xorhtml) = parseOptions()
  command = ""
  if options.x2h:
    command = "xsltproc -o %s Apathy2Xhtml.xsl %s"
  else:
    command = "xsltproc -o %s -html Xhtml2Apathy.xsl %s"

  command = command%(options.output_file,xorhtml)
  print command
  os.system(command)