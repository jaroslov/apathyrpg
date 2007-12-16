#!/usr/bin/python
import sys, os

from xml.dom.minidom import parse as parseXml

def buildOutLine(book):
  # recurses over the book and pulls out
  # part -> chapter -> section <-> section
  outline = {}
  tagname = ""
  if book.nodeType == book.ELEMENT_NODE:
    tagname = book.tagName
    if book.tagName in ["part","chapter","section"]:
      # find the title
      for child in book.childNodes:
        if child.nodeType == child.ELEMENT_NODE:
          if child.tagName == "title":
            tagname = child.firstChild.nodeValue
  if book.hasChildNodes:
    order = 0
    for child in book.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        if child.tagName in ["apathy-game","book","part","chapter","section"]:
          order += 1
          subline,tagn = buildOutLine(child)
          outline[(order,tagn)] = subline
  return (outline,tagname)

def printOutline(outline,indent="",number=[1]):
  keys = outline.keys()
  keys.sort()
  res = ""
  for key in keys:
    num = ""
    if len(number) > 0:
      num = str(number[0])
    for n in number[1:]:
      num += "."+str(n)
    res += indent + num + " " + key[1]
    subnum = [n for n in number]
    subnum.append(1)
    res += printOutline(outline[key],indent+"  ",subnum)
    number[-1] += 1
  return res 

def stripGameAndBook(outline):
  if type(outline) == tuple:
    outline = outline[0]
  keys = outline.keys()
  outline = outline[keys[0]]
  keys = outline.keys()
  return outline[keys[0]]

def buildText(book):
  # recurses over the book and pulls out
  # part -> chapter -> section <-> section
  outline = {}
  tagname = ""
  if book.nodeType == book.ELEMENT_NODE:
    tagname = book.tagName
    if book.tagName in ["part","chapter","section"]:
      # find the title
      for child in book.childNodes:
        if child.nodeType == child.ELEMENT_NODE:
          if child.tagName == "title":
            tagname = child.firstChild.nodeValue
  if book.hasChildNodes:
    order = 0
    for child in book.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        if child.tagName in ["apathy-game","book","part","chapter","section"]:
          order += 1
          subline,tagn = buildText(child)
          outline[(order,tagn)] = subline
        if child.tagName in ["text","note"]:
          order += 1
          outline[order] = child.toxml()
  return (outline,tagname)

def printText(book):
  return ""

def yesnoToBoolean(yn):
  if yn == "yes": return True
  return False

class Field(object):
  def __init__(self, Fld):
    self.Name = Fld.getAttribute("name")
    self.Title = ""
    if Fld.hasAttribute("title"):
      self.Title = yesnoToBoolean(Fld.getAttribute("title"))
    self.Table = False
    if Fld.hasAttribute("table"):
      self.Table = yesnoToBoolean(Fld.getAttribute("table"))
    self.ColumnFormat = ""
    if Fld.hasAttribute("colfmt"):
      self.ColumnFormat = Fld.getAttribute("colfmt")
    self.Description = False
    if Fld.hasAttribute("description"):
      self.Description = yesnoToBoolean(Fld.getAttribute("description"))
    self.Value = Fld.nodeValue
  def toString(self,indent=""):
    return indent+self.Name
  def __str__(self): return self.toString()
  def __repr__(self): return str(self)

class DefaultEntry(object):
  def __init__(self, Def):
    self.Fields = []
    for child in Def.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        self.Fields.append(Field(child))
  def toString(self,indent=""):
    res = ""
    if len(self.Fields) > 0:
      res = self.Fields[0].toString(indent+"  ")
    for fld in self.Fields[1:]:
      res += "\n"+fld.toString(indent+"  ")
    return res
  def __str__(self): return self.toString()
  def __repr__(self): return str(self)

class Category(object):
  def __init__(self, Cat):
    self.Name = Cat.getAttribute("name")
    self.Node = Cat
    self.Default = None
    for child in self.Node.childNodes:
      if child.nodeType == child.ELEMENT_NODE:
        if child.tagName == "default":
          self.Default = DefaultEntry(child)
  def toString(self, indent=""):
    res = self.Name
    res += "\n"+self.Default.toString(indent+"  ")
    return res
  def __str__(self): return self.toString()
  def __repr__(self): return str(self)

def getRawData(apathy):
  game = None
  for child in apathy.childNodes:
    game = child
  rawdata = None
  for child in game.childNodes:
    if child.nodeType == child.ELEMENT_NODE:
      if child.tagName == "raw-data":
        rawdata = child
  for child in rawdata.childNodes:
    if child.nodeType == child.ELEMENT_NODE:
      if child.tagName in ["category"]:
        cat = Category(child)
        print cat

apathy = parseXml("Apathy.xml")
outline = printOutline(stripGameAndBook(buildOutLine(apathy)))
#text = stripGameAndBook(buildText(apathy))
#printText(text)
getRawData(apathy)