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
    hmap = parse_handlers('default.hndl')
    options.handler = parse_handlers(options.handler, hmap)

  return options, args

def find_next_section(handlers, index, which):
  while index < len(handlers):
    handler = handlers[index]
    if which in handler and which+":" == handler[0:len(which)+1]:
      return index, handler[len(which)+1:].strip()
    index += 1
  return -1, None

def parse_handlers(filename, HandlerMap = {}):
  handlers = [s.strip() for s in open(filename, 'r').readlines()]
  hdx = 0
  while hdx < xrange(len(handlers)):
    hdx, Tag = find_next_section(handlers, hdx, "tag")
    if hdx == -1: break
    hdx, Open = find_next_section(handlers, hdx, "open")
    if hdx == -1: break
    hdx, In = find_next_section(handlers, hdx, "in")
    if hdx == -1: break
    hdx, Close = find_next_section(handlers, hdx, "close")
    if hdx == -1: break
    else: opendx = hdx
    if HandlerMap.has_key(Tag):
      HandlerMap[Tag]["Open"] = Open
      HandlerMap[Tag]["In"] = In
      HandlerMap[Tag]["Close"] = Close
    else:
      HandlerMap[Tag] = {"Open":Open, "In":In, "Close":Close}

  return HandlerMap

if __name__=="__main__":
  options, args = parseOptions()
