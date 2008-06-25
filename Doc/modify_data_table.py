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
  parser.add_option("-c","--duplicate-from-element",dest="duplicate",
                    help="the column to duplicate from", metavar="INTEGER")
  parser.add_option("-i","--insert",dest="insert",
                    help="the position to insert a column at", 
                    metavar="INTEGER")
  parser.add_option("-a","--append",dest="append",action="store_true",
                    help="append a column to the table",
                    metavar="INTEGER")
  parser.add_option("-d","--td-text",dest="td_text",
                    help="text to place into each column", 
                    metavar="HTML")
  parser.add_option("-r","--remove",dest="remove",
                    help="a column to remove", 
                    metavar="INTEGER")
  
  (options, args) = parser.parse_args()

  if len(args) != 1:
    print >> sys.stderr, "see --help"
    parser.print_help(sys.stderr)
    sys.exit(1)
  def bool_to_int(val):
    if val: return 1
    else: return 0
  vals = [bool_to_int(f is not None)
            for f in [options.insert, options.append, options.remove]]
  if sum(vals) != 1:
    print >> sys.stderr, "see --help"
    print >> sys.stderr, "The options 'insert', 'remove', and 'append' are mutually exclusive"
    parser.print_help(sys.stderr)
    sys.exit(1)

  return options, args[0]

def do_action(someRow, fragment, options):
  # find a td/th child:
  example = None
  duplicate = 0
  remove = 0
  if options.duplicate:
    duplicate = int(options.duplicate)
  if options.remove:
    remove = int(options.remove)
  offset = 0
  rmnode = 0
  tdoff = 0
  for is_ts in someRow.childNodes:
    if is_ts.nodeType == is_ts.ELEMENT_NODE and is_ts.tagName in ["th", "td"]:
      if offset == duplicate:
        example = is_ts.cloneNode(is_ts)
        if is_ts.tagName == "td":
          if fragment is not None:
            example.childNodes = [fragment]
          else:
            example.childNodes = []
      if tdoff == remove:
        rmnode = offset
      tdoff += 1
    offset += 1
  if options.insert:
    someRow.childNodes.insert(int(options.insert), example)
  elif options.append:
    someRow.appendChild(example)
  elif options.remove:
    someRow.childNodes.remove(someRow.childNodes[rmnode])


def make_fragment(text):
  if text is None:
    return None
  return parseString(text).childNodes[0]

def modify_file(xmlfile_name, options):
  xmlfile = parseXml(xmlfile_name)
  td_xml = make_fragment(options.td_text)
  for is_table in xmlfile.childNodes:
    if is_table.nodeType == is_table.ELEMENT_NODE:
      if "table" == is_table.tagName:
        for is_ts in is_table.childNodes:
          if is_ts.nodeType == is_ts.ELEMENT_NODE:
            if is_ts.tagName == "thead":
              do_action(is_ts, None, options)
            elif is_ts.tagName == "tbody":
              for is_tr in is_ts.childNodes:
                if is_tr.nodeType == is_tr.ELEMENT_NODE:
                  do_action(is_tr, td_xml, options)

if __name__=="__main__":
  options, xmlfile_name = parseOptions()

  modify_file(xmlfile_name, options)