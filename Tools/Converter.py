#!/usr/bin/env python2.5

import os, sys
from optparse import OptionParser

def parseOptions():
  parser = OptionParser()
  parser.add_option("-x","--xslt",dest="xslt",
          help="the xml stylesheet to use",metavar="FILE")
  parser.add_option("-s","--suffix",dest="suffix",
          help="the new suffix",metavar="FILE")
  parser.add_option("-p","--pretend",dest="pretend",
          help="generate command lines but don't execute",action="store_true")

  parser.usage += " Files..."

  (options, args) = parser.parse_args()

  return options, args

if __name__=="__main__":
  (options, files) = parseOptions()

  command = "xsltproc -o %s %s %s"

  for file in files:
    print file