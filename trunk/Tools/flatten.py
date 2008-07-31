#!/usr/bin/env python2.5

import os, sys
from lxml import etree

def actual_flatten(Node):
  ## given a node that is a DIV with @class in 'book' 'part' 'chapter' 'section'
  #  it takes the H1 and the DIV@class='section-body' and makes them into a list
  #  then recurses and concatenates
  if Node.tag == 'div' and Node.attrib.has_key('class') and Node.get('class') in ['book','part','chapter','section']:
    sectionkind = Node.get('class')
    if len(Node.xpath("parent::*/parent::*/parent::*/parent::div[@class='chapter']")) == 1:
      sectionkind = "subsection"
    if len(Node.xpath("parent::*/parent::*/parent::*/parent::*/parent::*/parent::div[@class='chapter']")) == 1:
      sectionkind = "subsubsection"
    title = Node.xpath("./h1")
    body =  Node.xpath("./div[@class='section-body']")
    result = []
    if Node.get('class') == 'book':
      auth = Node.xpath("./a")
      result.extend(auth)
    if len(title) == 1:
      title = title[0]
      title.set('class', sectionkind)
      result.append(title)
    if len(body) == 1:
      body = body[0]
      body = body.xpath("./*")
      result.extend(body)
    nresult = []
    for res in result:
      sresult = actual_flatten(res)
      nresult.extend(sresult)
    return nresult
  return [Node]

def flatten(Prefix, Filename):
  path = os.path.join(Prefix, Filename)

  Node = etree.parse(path).getroot()
  Subdocs = Node.xpath("//a[@class='in-place' and @href]")

  # nuke "div@class='example'"
  examples = Node.xpath("//div[@class='example']")
  for example in examples:
    example.tag = 'blockquote'

  result = actual_flatten(Node)
  wrap = etree.Element("root")
  wrap.set('class', 'wrapper')
  for res in result:
    wrap.append(res)
  newname = os.path.splitext(Filename)[0]+".xml"
  newfile = open(newname, "w")
  print >> newfile, '<?xml version="1.0"?>'
  print >> newfile, etree.tostring(wrap, pretty_print=True)
  newfile.close()
  os.system("xmllint --format %s > %s"%(newname, os.path.splitext(newname)[0]+".xhtml"))
  os.system("rm %s"%newname)
  
  for Subdoc in Subdocs:
    targsplit = list(os.path.split(Subdoc.get('href')))
    newname = targsplit[-1]
    restpref = targsplit[0:-1]
    newpref = Prefix
    for pref in restpref:
      newpref = os.path.join(newpref, pref)
    flatten(newpref, newname)

if __name__=="__main__":
  if len(sys.argv) == 3:
    Prefix = sys.argv[1]
    flatten(sys.argv[1], sys.argv[2])
