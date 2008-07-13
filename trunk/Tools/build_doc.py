#!/usr/bin/env python2.5

import os, sys
from optparse import OptionParser
from lxml import etree
from random import SystemRandom
import codecs
from Uni2LaTeX import unicodeToLaTeX

FASTHACK = False

HTMLNS = """http://www.w3.org/1999/xhtml"""
HTMLNSMap = {'x':HTMLNS}

def parseOptions():
  parser = OptionParser()
  parser.add_option("-p","--prefix",dest="prefix",
                    help="[required] the directory of the document",
                    metavar="FOLDER")
  parser.add_option("-o","--output",dest="output",
                    help="the name of the output file",
                    metavar="FILE")
  parser.add_option("-l","--latex",dest="latex",
                    action="store_true",help="produce LaTeX output")
  parser.add_option("-w","--xhtml",dest="xhtml",
                    action="store_true",help="produce webpage output")
  parser.add_option("","--lint",dest="lint",
                    action="store_true",
                    help="attempt to the clean the source files")
  parser.add_option("","--main-document",dest="main",
                    help="the name of the main document, defaults to 'Apathy'",
                    metavar="FILE")
  parser.add_option("","--retarget-resources",dest="retargetresources",
                    help="retarget image, css, etc. resources",
                    action="store_true")

  
  (options, args) = parser.parse_args()

  if options.prefix is None:
    print >> sys.stderr, 'You must give a source directory using "--prefix=???"',
    print >> sys.stderr, "see --help"
    sys.exit(1)
  if options.output is None:
    options.output = "Apathy"
  if options.latex is None:
    options.latex = False
  if options.xhtml is None:
    options.xhtml = False
  if options.lint is None:
    options.lint = False
  if options.main is None:
    options.main = "Apathy"
  if options.retargetresources is None:
    options.retargetresources = False

  return options, args

def xpath (Node, Path):
  return Node.xpath(Path, namespaces=HTMLNSMap)

def transform_table(subdoc, options):
  """
  Given a Category Table, convert it for display:
  (1) extract Title & Description and build per-entry information
  (2) remove all-Non-Table values
  """
  ths = subdoc.xpath("//th")
  title = subdoc.xpath("//th[@class='Title']")[0]
  description = subdoc.xpath("//th[@class='Description']")[0]
  tables = subdoc.xpath("//th[@class='Table']")
  rows = subdoc.xpath("//tr")
  print >> sys.stderr, len(rows)
  return subdoc

def combine_references(DocNode, options):
  hrids = DocNode.xpath("//a[@class='hrid']")
  for hrid in hrids:
    subdocname = os.path.join(options.prefix, hrid.attrib["href"])
    subdoc = etree.parse(subdocname).getroot()
    subdoc = transform_table(subdoc, options)
    ipparent = hrid.getparent()
    ipparent.replace(hrid, subdoc)
  return DocNode

def wrap_in_html(Node, options):
  wrapper = """<html xml:lang="en">
    <head>
      <title>Apathy Role Playing Game</title>
      <link rel="stylesheet" type="text/css"
            href="%s/Apathy.css" title="Apathy" />
    </head>
    <body>
      <combined-data-goes-here />
    </body>
  </html>
  """%options.prefix
  wrapnode = etree.fromstring(wrapper)
  cdgh = wrapnode.xpath("//combined-data-goes-here")[0]
  cdgh.getparent().insert(0, Node)
  html = wrapnode.xpath("//html")[0]
  html.set('xmlns', "http://www.w3.org/1999/xhtml")
  return wrapnode

def buildLatex(options): pass

def buildWebPage(options):
  # combine together
  docname = os.path.join(options.prefix, options.main+".xhtml")
  maindoc = etree.parse(docname)
  maindoc = combine_references(maindoc, options)
  maindoc = wrap_in_html(maindoc.getroot(), options)
  print >> sys.stdout, etree.tostring(maindoc)

if __name__=="__main__":
  options, args = parseOptions()

  if options.latex:
    buildLatex(options)
  if options.xhtml:
    buildWebPage(options)
