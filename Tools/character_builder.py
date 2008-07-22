#!/usr/bin/env python2.5
# -*- mode:python; tab-width:2; indent-tabs-mode:nil; -*-

import sys, os, codecs, curses
from lxml import etree

def get_all_categories(PrefixDir, Categories):
  folders = os.listdir(PrefixDir)
  for folder in folders:
    path = os.path.join(PrefixDir, folder)
    if os.path.isfile(path) and os.path.splitext(path)[-1] == ".xhtml":
      XML = etree.parse(path).getroot()
      if XML.tag == "table" and XML.attrib.has_key('class') and XML.get('class')=='category':
        catname = XML.get('name')
        catcat = XML.get('category')
        if catcat is None: catcat = 'unknown'
        trows = XML.xpath("//tr")
        if len(trows) > 0:
          Categories[catcat+'.'+catname] = XML
    if os.path.isdir(path):
      get_all_categories(path, Categories)

def run_builder(Categories, Screen):
  options = ["Set Character Info",
             "Set Attributes",
             "Set Skills",
             "Set Spells",
             "Set Equipment",
             "Quit"]
  for idx in xrange(len(options)):
    Screen.addstr(idx*2+2,2,"(%d) %s"%(idx+1,options[idx]))
  while 1:
    c = Screen.getch()
    if c in [ord('q'), ord('Q')]: break
    else: continue

def start_builder(Categories):
  stdscr = curses.initscr()
  curses.noecho()
  curses.cbreak()
  stdscr.keypad(1)

  try:
    run_builder(Categories, stdscr)
  except:
    raise Exception, "CURSES Exception occurred"

  curses.nocbreak()
  stdscr.keypad(0)
  curses.echo()
  curses.endwin()

if __name__=="__main__":
  prefix = "NO PREFIX"
  if len(sys.argv) == 2:
    prefix = sys.argv[1]
    categories = {}
    get_all_categories(prefix, categories)
    start_builder(categories)
