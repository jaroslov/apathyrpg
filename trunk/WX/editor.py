#!/usr/bin/env pythonw

import wx
import sys
from xml.dom.minidom import parse as parseXml

class editor(wx.Frame):
  def __init__(self,From,To):
    Display = wx.DisplaySize()
    pos = ((Display[0]-800)/2,(Display[1]-600)/2)
    wx.Frame.__init__(self, None, wx.NewId(), "ARPG-MS",
                      pos=pos,
                      size=(800,600),
                      style=wx.DEFAULT_FRAME_STYLE|wx.FRAME_EX_METAL|wx.TAB_TRAVERSAL)
    self.DisplaySizer = wx.BoxSizer(wx.VERTICAL)
    self.From = From
    self.To = To

    self.Hierarchy = None

    res = self.AddHierarchy(self.Hierarchy,Depth=1000)
    
  def AddHierarchy(self,Hierarchy=None,XML=None,Depth=1):
    if Depth <= 0:
      return
    if not XML:
      XML = parseXml("../Apathy.xml")
    if XML.nodeType == XML.ELEMENT_NODE:
      SubRoot = self.Hierarchy.AppendItem(Hierarchy,
                  XML.tagName,data=wx.TreeItemData(None))
      for child in XML.childNodes:
        self.AddHierarchy(SubRoot,XML=child,Depth=Depth-1)
    elif XML.nodeType == XML.DOCUMENT_NODE:
      self.Hierarchy = wx.TreeCtrl(self)
      Root = self.Hierarchy.AddRoot("XML")
      for child in XML.childNodes:
        self.AddHierarchy(Hierarchy=Root,XML=child,Depth=Depth)


def run():
  app = wx.App()
  Ed = editor("../Apathy.xml","Apathy.xml")
  Ed.Show(True)
  app.MainLoop()

if __name__=="__main__":
  run()