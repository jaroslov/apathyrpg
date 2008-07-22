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

def donothing(X): pass

def quit(X): raise Exception, "Quit"

def run_builder(Categories, Screen):
  mainmenu = [("Set Character Info",donothing),
              ("Set Attributes",donothing),
              ("Set Skills",donothing),
              ("Set Spells",donothing),
              ("Set Equipment",donothing),
              ("Quit",quit)]
  Menus = {"mainmenu":mainmenu}
  Menu = Menus["mainmenu"]
  Cursor = (0,0)
  Written = ""
  while 1:
    numoptions = len(Menu)
    for idx in xrange(numoptions):
      key = Menu[idx][0]
      Screen.addstr(idx*2+2,4,"(%d) %s"%(idx+1, key))
    Screen.addstr(idx*2+4,4,"Enter:")
    Cursor = (idx*2+4,4+len("Enter:"+Written)+1)
    Screen.move(Cursor[0],Cursor[1])
    c = Screen.getch()
    Written += chr(c)
    Screen.addstr(Cursor[0],Cursor[1],"%s"%chr(c))
    Cursor = (Cursor[0],Cursor[1]+1)
    

def start_builder(Categories):
  stdscr = curses.initscr()
  curses.noecho()
  curses.cbreak()
  stdscr.keypad(1)

  try:
    run_builder(Categories, stdscr)
  except:
    print >> sys.stderr, "CURSES Exception occurred"

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
