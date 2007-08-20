import sys
import wx
from ArpgMs import ARPG_MS as ArpgContent

# This is a windowed utility for modifying the contents
# but *not* the hierarchy of the Data-section of the 
# ARPG-MS.
#
# We have a window divided into two parts: on the left is
# a scrolling TreeCtl with the hierarchy
# On the right, if something is selected is item to be
# modified. There is also a capability to "add new"
# which is based upon objects within the hierarchy, already.

class ArpgXMLReference(wx.TreeItemData):
  def __init__(self, Reference=None, Key=""):
    wx.TreeItemData.__init__ (self)
    self.Reference = Reference
    self.Key = ""

class SelectionFrame(wx.Frame):
  def __init__(self, ArpgContent):
    wx.Frame.__init__(self, None, wx.NewId(), "ARPG-MS",
                          pos=wx.DefaultPosition,
                          style=wx.DEFAULT_FRAME_STYLE|wx.TAB_TRAVERSAL)

    self.ArpgContent = ArpgContent

    self.Splitter = wx.SplitterWindow (self, wx.ID_ANY)
    self.LeftPanel = wx.Panel (self.Splitter, wx.ID_ANY)
    self.RightPanel = wx.Panel (self.Splitter, wx.ID_ANY)

    self.Hierarch = None
    self.AddHierarchy ()
    try:
      self.Bind (wx.EVT_TREE_SEL_CHANGED, self.Selected, self.Hierarch)
    except:
      pass
    
    self.TextBox = wx.TextCtrl (self.RightPanel,
                                style=wx.TE_WORDWRAP|wx.TE_MULTILINE)
    self.Bind (wx.EVT_TEXT, self.TextChanged, self.TextBox)

    #self.Splitter = wx.SplitterWindow (self, wx.NewId())
    #try:
    #  self.SplitVertically (self.Hierarch, self.TextBox, -100)
    #except:
    #  pass

    if False:
      self.Sizer = wx.BoxSizer (wx.HORIZONTAL)
      self.Sizer.Add (self.Hierarch,
                      proportion=1,
                      flag=wx.EXPAND
                      | wx.ALL
                      | wx.ALIGN_CENTER_HORIZONTAL
                      | wx.ALIGN_CENTER_VERTICAL,
                      border=5)
      self.Sizer.Add (self.TextBox,
                      proportion=1,
                      flag=wx.EXPAND
                      | wx.ALL
                      | wx.ALIGN_CENTER_HORIZONTAL
                      | wx.ALIGN_CENTER_VERTICAL,
                      border=15)
      
      self.SetSizer (self.Sizer)
      self.SetAutoLayout (1)
      self.Sizer.Fit (self)
    else:
      self.LSizer = wx.BoxSizer (wx.HORIZONTAL)
      self.LSizer.Add (self.Hierarch,
                      proportion=1,
                      flag=wx.EXPAND
                      | wx.ALL
                      | wx.ALIGN_CENTER_HORIZONTAL
                      | wx.ALIGN_CENTER_VERTICAL,
                      border=5)
      self.LeftPanel.SetSizer (self.LSizer)
      self.RSizer = wx.BoxSizer (wx.HORIZONTAL)
      self.RSizer.Add (self.TextBox,
                      proportion=1,
                      flag=wx.EXPAND
                      | wx.ALL
                      | wx.ALIGN_CENTER_HORIZONTAL
                      | wx.ALIGN_CENTER_VERTICAL,
                      border=5)
      self.RightPanel.SetSizer (self.RSizer)
      self.Splitter.SplitVertically (self.LeftPanel, self.RightPanel, -100)

    self.Show (True)

  def AddHierarchy (self, Root=None, What=None):
    if self.Hierarch:
      if "Hierarch" == What.Kind:
        Name = What.Name
        if len(What.ID) > 0:
          Name = What.ID
        SubRoot = self.Hierarch.AppendItem (Root, Name)
        Keys = What.Data.keys  ()
        Keys.sort ()
        for Key in Keys:
          self.AddHierarchy (SubRoot, What.Data[Key])
      elif "KeyedItem" == What.Kind:
        self.AddHierarchy (Root, What.Data)
      else:
        if len(What.Data["name"]) > 0:
          SubRoot = self.Hierarch.AppendItem (Root, What.Data["name"])
          TSKeys = What.TopoSortKeys ()
          for TSKey in TSKeys:
            datum = ArpgXMLReference (What, TSKey)
            self.Hierarch.AppendItem (SubRoot, TSKey.capitalize (),
                                      data=wx.TreeItemData((What,TSKey)))
    else:
      self.Hierarch = wx.TreeCtrl (self.LeftPanel)
      Root = self.Hierarch.AddRoot (self.ArpgContent.Name)
      Keys = self.ArpgContent.Data.keys ()
      Keys.sort ()
      for Key in Keys:
        self.AddHierarchy (Root, self.ArpgContent.Data[Key])

  def Selected (self, Event):
    which = self.Hierarch.GetSelection ()
    itemdatum = self.Hierarch.GetItemData (which)
    datum = itemdatum.GetData ()
    if datum:
      self.TextBox.SetValue (datum[0].Data[datum[1]])
    else:
      self.TextBox.SetValue ("")

  def TextChanged (self, Event):
    which = self.Hierarch.GetSelection ()
    itemdatum = self.Hierarch.GetItemData (which)
    datum = itemdatum.GetData ()
    if datum:
      datum[0].Data[datum[1]] = self.TextBox.GetValue ()

if __name__=="__main__":
  app = wx.App ()
  ArpgContent = ArpgContent (sys.argv[1])
  sf = SelectionFrame (ArpgContent)
  app.MainLoop ()
  if len(sys.argv) > 2:
    tmp = open (sys.argv[2], "w")
    print >> tmp, ArpgContent.AsXML ()
