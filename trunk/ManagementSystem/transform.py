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
  for key in keys:
    num = ""
    if len(number) > 0:
      num = str(number[0])
    for n in number[1:]:
      num += "."+str(n)
    print indent + num + " " + key[1]
    subnum = [n for n in number]
    subnum.append(1)
    printOutline(outline[key],indent+"  ",subnum)
    number[-1] += 1

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

apathy = parseXml("Apathy.xml")
outline = stripGameAndBook(buildOutLine(apathy))

#printOutline(outline)
text = stripGameAndBook(buildText(apathy))
printText(text)