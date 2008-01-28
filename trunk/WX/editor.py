#!/usr/bin/env pythonw

import wx
from wx.lib.foldpanelbar import FoldPanelBar as wxFoldPanelBar
import sys
from xml.dom.minidom import parse as parseXml

def EnforceXmlId(Node,Id=[1],SerializeChildren=None):
  if Node.nodeType == Node.ELEMENT_NODE:
    if not Node.attributes.has_key("xml:id"):
      Node.attributes["xml:id"] = ""
    Attr = ("G%7s"%(str(Id[0]))).replace(" ","0")
    Node.setAttribute("xml:id",Attr)
    Node.setIdAttribute("xml:id")
    Id[0] += 1
  if Node.nodeType in [Node.DOCUMENT_NODE,Node.ELEMENT_NODE]:
    if (Node.nodeType == Node.ELEMENT_NODE
        and SerializeChildren
        and Node.tagName in SerializeChildren):
      return
    for child in Node.childNodes:
      EnforceXmlId(child,Id=Id,SerializeChildren=SerializeChildren)

def serializeChildren(Node):
  result = ""
  for child in Node.childNodes:
    result += child.toxml()
  return result

class ItemData(object):
  def __init__(self):
    self.Id = ""

class Outline(wx.TreeCtrl):
  def __init__(self,Parent,XML,SerializeChildren=None):
    wx.TreeCtrl.__init__(self,Parent,
        style=wx.TR_SINGLE
             |wx.TR_EDIT_LABELS
             |wx.TR_HAS_BUTTONS
             |wx.TR_HAS_VARIABLE_ROW_HEIGHT
             |wx.TR_HAS_BUTTONS)
    self.SerializeChildren = SerializeChildren 
    if not SerializeChildren: self.SerializeChildren = []
    self.loadHierarchy(XML)
  def loadHierarchy(self,Node,Hierarchy=None,IsRoot=False,Id=[1]):
    result = []
    try:
      if Node.nodeType == Node.ELEMENT_NODE:
        if IsRoot:
          SubRoot = self.AddRoot(text=Node.tagName)
        else:
          SubRoot = self.AppendItem(Hierarchy,
                      text=Node.tagName)
        data = ItemData()
        data.Id = Node.getAttribute("xml:id")
        data.Text = ""
        if Node.tagName in self.SerializeChildren:
          pass # nothing to do here, right now
        else:
          for child in Node.childNodes:
            res = self.loadHierarchy(Hierarchy=SubRoot,Node=child)
        # set the Item Data
        self.SetItemData(SubRoot,wx.TreeItemData(data))
      elif Node.nodeType == Node.DOCUMENT_NODE:
        for child in Node.childNodes:
          self.loadHierarchy(Hierarchy=None,Node=child,IsRoot=True)
      elif Node.nodeType == Node.TEXT_NODE:
        result.append(Node.nodeValue)
    except Exception, e:
      print >> sys.stderr, e
    return result

class outliner(wx.Dialog):
  def __init__(self,Parent,XML,SerializeChildren):
    wx.Dialog.__init__(self, Parent, wx.NewId(), "Outline",
                      style=wx.CAPTION|wx.NO_3D|wx.DIALOG_EX_METAL)
    self.Superior = Parent
    self.Outline = Outline(self,XML,SerializeChildren=SerializeChildren)
    self.Bind(wx.EVT_TREE_END_LABEL_EDIT, self.LabelEdit, self.Outline)
    self.Data = None
  def LabelEdit(self, event):
    self.Data = self.Outline.GetItemData(event.GetItem()).GetData()
    self.Superior.LabelEdit()
  def GetData(self):
    return self.Data

class XMLEditorPane(wx.VScrolledWindow):
  def __init__(self,Parent,XML,SerializeChildren,Depth=0):
    wx.VScrolledWindow.__init__(self,Parent,
                      style=wx.TAB_TRAVERSAL
                           |wx.RAISED_BORDER)
    self.ChildSizer = wx.BoxSizer(orient=wx.VERTICAL)

    self.buildPanes(XML,SerializeChildren,Depth)

    self.SetSizer(self.ChildSizer)
    self.SetAutoLayout(1)
    self.ChildSizer.Fit(self)

  def buildPanes(self,Node,SerializeChildren,Depth):
    if Depth > 6:
      return
    if Node.nodeType == Node.DOCUMENT_NODE:
      pass
    elif Node.nodeType == Node.ELEMENT_NODE:
      if Node.tagName in SerializeChildren:
        Text = serializeChildren(Node)
        self.ChildSizer.Add(wx.TextCtrl(self,value=serializeChildren(Node)))
      else:
        self.ChildSizer.Add(wx.StaticText(self,label=Node.tagName))
      if Node.tagName in SerializeChildren:
        return
    if Node.nodeType in [Node.DOCUMENT_NODE,Node.ELEMENT_NODE]:
      for child in Node.childNodes:
        if child.nodeType == Node.ELEMENT_NODE:
          child_pane = XMLEditorPane(self,child,SerializeChildren,Depth+1)
          self.ChildSizer.Add(child_pane,flag=wx.LEFT,border=5)

class editor(wx.Frame):
  def __init__(self,From,To,SerializeChildren=None):
    Display = wx.DisplaySize()
    pos = ((Display[0]-800)/2,(Display[1]-600)/2)
    wx.Frame.__init__(self, None, wx.NewId(), "ARPG-MS",
                      pos=pos,
                      size=(800,600),
                      style=wx.DEFAULT_FRAME_STYLE
                           |wx.FRAME_EX_METAL
                           |wx.TAB_TRAVERSAL)
    self.SetMinSize((800,600))

    self.DisplaySizer = wx.BoxSizer(wx.VERTICAL)
    self.From = From
    self.To = To
    self.SerializeChildren = SerializeChildren

    # open the XML file and make sure that it contains unique xml:ids
    self.XML = parseXml(self.From)
    EnforceXmlId(self.XML,SerializeChildren=self.SerializeChildren)

    # build the outline
    self.Outline = outliner(self,self.XML,self.SerializeChildren)

    # actually build the XML viewer here
    self.Editor = XMLEditorPane(self,self.XML,self.SerializeChildren)

  def Display(self):
    self.Show(True)
    self.Outline.Show(True)
  def LabelEdit(self):
    data = self.Outline.GetData()
    element = self.XML.getElementById(data.Id)
    if (element.nodeType == element.ELEMENT_NODE
        and element.tagName in self.SerializeChildren):
      print >> sys.stderr, serializeChildren(element)

def run():
  app = wx.App()
  try:
    Ed = editor("TruncApathy.xml","Apathy.xml",["text"])
    Ed.Display()
  except Exception, e:
    print sys.stderr, e
  app.MainLoop()

if __name__=="__main__":
  run()