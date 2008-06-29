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
  parser.add_option("-c","--combine",dest="combine",
                    action="store_true",help="produce a single source file")
  parser.add_option("-l","--latex",dest="latex",
                    action="store_true",help="produce LaTeX output")
  parser.add_option("-w","--xhtml",dest="xhtml",
                    action="store_true",help="produce webpage output")
  parser.add_option("","--clean",dest="clean",
                    action="store_true",
                    help="attempt to the clean the source files")
  parser.add_option("","--main-document",dest="main",
                    help="the name of the main document, defaults to 'Apathy'",
                    metavar="FILE")
  parser.add_option("","--pretty-print",dest="prettyprint",
                    help="pretty-print the resulting file",action="store_true")
  parser.add_option("","--retarget-resources",dest="retargetresources",
                    help="retarget image, css, etc. resources",
                    action="store_true")
  parser.add_option("","--fast-hack",dest="fasthack",
                    help="debug option to speed generation of sources",
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
  if options.combine is None:
    options.combine = False
  if options.clean is None:
    options.clean = False
  if options.main is None:
    options.main = "Apathy"
  if options.prettyprint is None:
    options.prettyprint = False
  if options.retargetresources is None:
    options.retargetresources = False
  if options.fasthack is None:
    FASTHACK = False
  else:
    FASTHACK = True

  return options, args

def xpath (Node, Path):
  return Node.xpath(Path, namespaces=HTMLNSMap)

def inplace_combine(DocNode, options):
  inplaces = xpath(DocNode, "//x:a[@class='in-place']")
  for inplace in inplaces:
    subdocname = os.path.join(options.prefix, inplace.attrib["href"])
    subdoc = etree.parse(subdocname).getroot()
    ipparent = inplace.getparent()
    ipparent.replace(inplace, subdoc)
    print etree.tostring(ipparent)
  return DocNode

def buildLatex(options): pass

def buildWebPage(options):
  # combine together
  docname = os.path.join(options.prefix, options.main+".xhtml")
  maindoc = etree.parse(docname)
  maindoc = inplace_combine(maindoc, options)

if __name__=="__main__":
  options, args = parseOptions()

  if options.latex:
    buildLatex(options)
  if options.xhtml:
    buildWebPage(options)