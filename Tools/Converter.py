#!/usr/bin/env python2.5

import os, sys
from optparse import OptionParser

def parseOptions():
  parser = OptionParser()
  parser.add_option("-x","--xslt",dest="xslt",
          help="[required] the xml stylesheet to use",metavar="FILE")
  parser.add_option("-s","--suffix",dest="suffix",
          help="the new suffix",metavar="FILE")
  parser.add_option("-p","--pretend",dest="pretend",
          help="generate command lines but don't execute",action="store_true")

  parser.usage += " Files..."

  (options, args) = parser.parse_args()

  if options.pretend is None:
    options.pretend = False

  if options.xslt is None:
    parser.print_help()
    sys.exit(1)

  if options.suffix is None:
    xsltext = os.path.splitext(os.path.split(options.xslt)[1])[0]
    options.suffix = xsltext.lower()

  return options, args

if __name__=="__main__":
  (options, files) = parseOptions()

  command = "xsltproc -o %s %s %s"

  for file in files:
    filepathparts = os.path.split(file)
    filename = filepathparts[1]
    filext = os.path.splitext(filename)
    newname = filext[0]+"."+options.suffix+filext[1]
    syscmd = command%(newname,options.xslt,file)
    if options.pretend:
      print syscmd
    else:
      os.system(syscmd)


