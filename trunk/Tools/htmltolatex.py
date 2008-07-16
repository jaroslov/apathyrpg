import sys, os
from optparse import OptionParser
from lxml import etree
import codecs
from Uni2LaTeX import unicodeToLaTeX, initialize_mapping

def parseOptions():
  initialize_mapping('.')

  parser = OptionParser()
  parser.add_option("-p","--prefix",dest="prefix",
                    help="(optional) directory of the document(s); every *.[x]htm[l] is captured and converted",
                    metavar="FOLDER")
  parser.add_option("-o","--output",dest="output",
                    help="the name of the output file",
                    metavar="FILE")
  parser.add_option("-x","--handler-file",dest="handler",
                    help="alternate handler file (from default)",
                    metavar="FILE")
  
  (options, args) = parser.parse_args()

  if options.prefix is None:
    options.prefix = ""
  if options.output is None:
    options.output = sys.stderr
  else:
    options.output = open(options.output, 'w')
  if options.handler is None:
    options.handler = parse_handlers('default.hndl')
  else:
    options.handler = parse_handlers(options.handler)

  return options, args

def parse_handlers(filename):
  handlers = [s.strip() for s in open(filename, 'r').readlines()]
  print handlers

if __name__=="__main__":
  options, args = parseOptions()
