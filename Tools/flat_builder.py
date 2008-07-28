import os, sys, random, copy, string
from optparse import OptionParser
from lxml import etree
import codecs
from Uni2LaTeX import unicodeToLaTeX, initialize_mapping

ERRORFILE = sys.stderr

def parseOptions():
  parser = OptionParser()
  parser.add_option("-p","--prefix",dest="prefix",
                    help="[required] the directory of the document",
                    metavar="FOLDER")
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
  parser.add_option("","--time-period",dest="time_period",
                    help="keeps only those sections before a certain time (date)",
                    metavar="INTEGER")
  parser.add_option("","--category-exclusion-list",dest="exclude",
                    help="A file of which categories to exclude from the final document",
                    metavar="EXCLUSION LIST")
  parser.add_option("","--list-categories",dest="list_categories",
                    help="Lists the set of categories in the document",
                    action="store_true")

  
  (options, args) = parser.parse_args()

  if options.prefix is None:
    print >> ERRORFILE, 'You must give a source directory using "--prefix=???"',
    print >> ERRORFILE, "see --help"
    sys.exit(1)
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
  try: # convert time-period to an integer
    options.time_period = int(options.time_period)
  except:
    options.time_period = 1000000000000
  if options.exclude is None:
    options.exclude = []
  else:
    options.exclude = [s.strip() for s in open(options.exclude, "r").readlines()]
  if options.list_categories is None:
    options.list_categories = False

  return options, args

def combine(options, args):
  """
  """

def run(options, args):
  combined = combine(options, args)

if __name__=="__main__":
  options, args = parseOptions()
  run(options, args)
