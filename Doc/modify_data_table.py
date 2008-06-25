#!/usr/bin/env python2.5

import os, sys
from xml.dom.minidom import parse as parseXml
from xml.dom.minidom import parseString
from optparse import OptionParser
import codecs

def parseOptions():
  parser = OptionParser()
  parser.add_option("-o","--output",dest="output",
                    help="the output file name", metavar="FILE")
  parser.add_option("-i","--insert",dest="insert",
                    help="the position to insert a column at", 
                    metavar="INTEGER")
  parser.add_option("-a","--append",dest="append",action="store_true",
                    help="append a column to the table",
                    metavar="INTEGER")
  parser.add_option("-d","--td-text",dest="td_text",
                    help="text to place into each column", 
                    metavar="HTML")
  parser.add_option("-t","--th-text",dest="th_text",
                    help="text to place into the header of each column", 
                    metavar="HTML")
  parser.add_option("-r","--remove",dest="remove",
                    help="a column to remove", 
                    metavar="INTEGER")
  
  (options, args) = parser.parse_args()

  if len(args) != 1:
    print >> sys.stderr, "see --help"
    parser.print_help(sys.stderr)
    sys.exit(1)

  return options, args[0]

def do_action(someRow, fragment, options):
  # find a td/th child:
  example = None
  for is_ts in someRow.childNodes:
    if is_ts.nodeType == is_ts.ELEMENT_NODE and is_ts.tagName in ["th", "td"]:
      example = is_ts.cloneNode(is_ts)
      break
  if fragment is not None:
    example.childNodes = [fragment]
  else:
    example.childNodes = []
  if options.insert:
    print "insert", options.insert
  elif options.append:
    someRow.appendChild(example)
    for item in someRow.attributes.items():
      print item
  elif options.remove:
    print "remove", options.remove

def make_fragment(text):
  if text is None:
    return None
  return parseString(text).childNodes[0]

def modify_file(xmlfile_name, options):
  xmlfile = parseXml(xmlfile_name)
  td_xml = make_fragment(options.td_text)
  th_xml = make_fragment(options.th_text)
  for is_table in xmlfile.childNodes:
    if is_table.nodeType == is_table.ELEMENT_NODE:
      if "table" == is_table.tagName:
        for is_ts in is_table.childNodes:
          if is_ts.nodeType == is_ts.ELEMENT_NODE:
            if is_ts.tagName == "thead":
              do_action(is_ts, th_xml, options)
            elif is_ts.tagName == "tbody":
              for is_tr in is_ts.childNodes:
                if is_tr.nodeType == is_tr.ELEMENT_NODE:
                  do_action(is_tr, td_xml, options)

if __name__=="__main__":
  options, xmlfile_name = parseOptions()

  modify_file(xmlfile_name, options)