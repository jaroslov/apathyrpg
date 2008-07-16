#!/usr/bin/env python2.5

import os, sys
from xml.dom.minidom import parse as parseXml
from xml.dom.minidom import parseString
from optparse import OptionParser
import codecs

USAGE_MESSAGE = """How to use this tool:

This tool is designed to allow you to easily add or remove columns from an
Apathy Data Table. To add a column do the following:

1. Give the name of the Apathy Data Table to modify (or standard input)
2. Choose insertion method: --append or --insert
3. If you chose to "insert", choose a 0-offset position to insert at; for
   example, --insert=0 (-i0) means to insert before the first column.
4. Optional: choose a column to clone, --clone=4 (clones the 5th column)
5. Optional: choose default body of the <td> element:
      --td-text="<p>Foo</p>"
   This must be valid HTML or XML
6. Optional: choose the name of an output file to write to (can be source file).
   If the file is written to disk, `xmllint --format` will be called on it,
   automatically.

Example:
>>> %s Equipment-Melee-Blunt.xhtml --append --clone=3 --td-text="<p>None.</p>" \\
     --output="Equipment-Melee-Blunt.xhtml"
"""%(sys.argv[0])

def parseOptions():
  parser = OptionParser()
  parser.add_option("-u","--usage",dest="usage",help="explain how to use",
                    action="store_true", metavar="SWITCH")
  parser.add_option("-o","--output",dest="output",
                    help="the output file name", metavar="FILE")
  parser.add_option("-c","--clone",dest="duplicate",
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
  rmnode = 0
  if options.duplicate:
    duplicate = int(options.duplicate)
  if options.remove:
    remove = int(options.remove)
  td_off = 0
  ttl_off = 0
  for is_ts in someRow.childNodes:
    if is_ts.nodeType == is_ts.ELEMENT_NODE and is_ts.tagName in ["th", "td"]:
      if td_off == duplicate:
        example = is_ts.cloneNode(is_ts)
        if is_ts.tagName == "td":
          if fragment is not None:
            example.childNodes = [fragment]
          else:
            example.childNodes = []
      if td_off == remove:
        rmnode = ttl_off
      td_off += 1
    ttl_off += 1
  if options.insert:
    someRow.childNodes.insert(int(options.insert), example)
  elif options.append:
    someRow.childNodes.append(example)
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
  ofile = sys.stdout
  lint = False
  if options.output:
    ofile = file(options.output, "w")
    lint = True
  print >> ofile, xmlfile.toxml(encoding="utf-8")
  ofile.close()
  if lint:
    os.system("xmllint --xmlout --format %s -o %s"%(options.output, options.output))

if __name__=="__main__":
  options, xmlfile_name = parseOptions()

  modify_file(xmlfile_name, options)
