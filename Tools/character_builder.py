#!/usr/bin/env python2.5
# -*- mode:python; tab-width:2; indent-tabs-mode:nil; -*-

import sys, os, codecs, curses, pygtk
from lxml import etree

pygtk.require('2.0')
import gtk

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

def start_builder(categories, character_sheet):
  pass

if __name__=="__main__":
  prefix = "NO PREFIX"
  if len(sys.argv) == 2:
    prefix = sys.argv[1]
    categories = {}
    get_all_categories(prefix, categories)
    character_sheet = etree.parse(os.path.join(prefix, "../Character.xhtml"))
    start_builder(categories, character_sheet)
