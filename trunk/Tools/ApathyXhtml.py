#!/usr/bin/env python2.5

import os, sys, libxslt, libxml2
from optparse import OptionParser

def parseOptions():
  parser = OptionParser()
  parser.add_option("-o","--output-file",dest="output_file",
          help="the output file name",metavar="FILE")

  parser.add_option("-x","--xml-to-xhtml",dest="x2h",
          help="force conversion: xml->xhtml",action="store_true")

  parser.add_option("-t","--xhtml-to-xml",dest="x2h",
          help="force conversion: xhtml->xml",action="store_false")

  parser.add_option("-s","--alt-xslt",dest="xslt",
          help="an alternate xslt to the default",metavar="FILE")

  parser.add_option("-p","--pretend",dest="pretend",
          help="emit command line, but do not run",action="store_true")

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

  xslt = ""

  # automatic generation of output file name
  if options.output_file is None:
    suffix = os.path.splitext(args[0])
    if options.x2h: suffix = (suffix[0],".xhtml")
    else: suffix = (suffix[0],".xml")
    options.output_file = suffix[0]+suffix[1]

  # set the xslt sheet
  if options.x2h: xslt = "Apathy2Xhtml.xsl"
  else: xslt = "Xhtml2Apathy.xsl"

  # alternate style sheet
  if options.xslt is None:
    options.xslt = xslt

  return options, args[0]

if __name__=="__main__":
  (options, xorhtml) = parseOptions()

  #stylexml   = libxml2.parseFile(options.xslt)
  #stylesheet = libxslt.parseStylesheetDoc(stylexml)
  #document   = libxml2.parseFile(xorhtml)
  #output     = stylesheet.applyStylesheet(document, None)
  #stylesheet.saveResultToFilename(options.output_file,output,0)
  #stylesheet.freeStylesheet()
  #document.freeDoc()
  #output.freeDoc()

  command = ""
  if options.x2h:
    command = "xsltproc -o %s %s %s"
  else:
    command = "xsltproc -o %s -html %s %s"

  command = command%(options.output_file,options.xslt,xorhtml)
  if options.pretend:
    print "PRETEND", command
  else:
    os.system(command)