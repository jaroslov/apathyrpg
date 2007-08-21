import sys
import wx
from ArpgMs import ARPG_MS as ArpgContent
import wx.lib.mixins.listctrl as listmix

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

class TextListCtrl(wx.ListCtrl,
                   listmix.ListCtrlAutoWidthMixin,
                   listmix.TextEditMixin):
  def __init__(self, parent, ID, pos=wx.DefaultPosition,
               size=wx.DefaultSize, style=0):
    wx.ListCtrl.__init__ (self, parent, ID, pos, size, style)
    listmix.ListCtrlAutoWidthMixin.__init__ (self)
    listmix.TextEditMixin.__init__ (self)

class SelectionFrame(wx.Frame):
  def __init__(self, ArpgContent):
    wx.Frame.__init__(self, None, wx.NewId(), "ARPG-MS",
                          pos=wx.DefaultPosition,
                          style=wx.DEFAULT_FRAME_STYLE|wx.TAB_TRAVERSAL)

    self.ArpgContent = ArpgContent

    self.MainSplitter = wx.SplitterWindow (self, wx.ID_ANY)
    self.LeftPanel = wx.Panel (self.MainSplitter, wx.ID_ANY)
    self.RightPanel = wx.Panel (self.MainSplitter, wx.ID_ANY)

    self.RightSplitter = wx.SplitterWindow (self.MainSplitter, wx.ID_ANY)
    self.TopPanel = wx.Panel (self.RightSplitter, wx.ID_ANY)
    self.BottomPanel = wx.Panel (self.RightSplitter, wx.ID_ANY)

    self.Hierarch = None
    self.AddHierarchy ()
    try:
      self.Bind (wx.EVT_TREE_SEL_CHANGED, self.Selected, self.Hierarch)
    except:
      pass
    
    self.ListView = TextListCtrl (self.TopPanel,
                                  wx.ID_ANY,
                                  style=wx.LC_REPORT
                                  | wx.BORDER_NONE
                                  | wx.LC_SORT_ASCENDING)
    self.Bind (wx.EVT_LIST_ITEM_SELECTED, self.ItemSelected, self.ListView)
    self.ItemCategory = None

    self.TextBox = wx.TextCtrl (self.BottomPanel,
                                style=wx.TE_WORDWRAP|wx.TE_MULTILINE)
    self.Bind (wx.EVT_TEXT, self.TextChanged, self.TextBox)

    self.LSizer = wx.BoxSizer (wx.HORIZONTAL)
    self.LSizer.Add (self.Hierarch,
                     proportion=1,
                     flag=wx.EXPAND
                     | wx.ALL
                     | wx.ALIGN_CENTER_HORIZONTAL
                     | wx.ALIGN_CENTER_VERTICAL,
                     border=5)
    self.LeftPanel.SetSizer (self.LSizer)

    self.TRSizer = wx.BoxSizer (wx.VERTICAL)
    self.TRSizer.Add (self.ListView,
                      proportion=3,
                      flag=wx.EXPAND
                      | wx.ALL
                      | wx.ALIGN_CENTER_HORIZONTAL
                      | wx.ALIGN_CENTER_VERTICAL,
                      border=5)
    self.TopPanel.SetSizer (self.TRSizer)

    self.BRSizer = wx.BoxSizer (wx.VERTICAL)
    self.BRSizer.Add (self.TextBox,
                      proportion=1,
                      flag=wx.EXPAND
                      | wx.ALL
                      | wx.ALIGN_CENTER_HORIZONTAL
                      | wx.ALIGN_CENTER_VERTICAL,
                      border=5)
    self.BottomPanel.SetSizer (self.BRSizer)

    self.RightSplitter.SplitHorizontally (self.TopPanel, self.BottomPanel, -100)
    self.RightSplitter.SetSashGravity (1.0)
    self.MainSplitter.SplitVertically (self.LeftPanel, self.RightSplitter, -200)
    
    self.Show (True)

  def AddHierarchy (self, Root=None, What=None):
    if self.Hierarch:
      if "Hierarch" == What.Kind:
        Name = What.Name
        if len(What.ID) > 0:
          Name = What.ID
        SubRoot = self.Hierarch.AppendItem (Root, Name,
                                            data=wx.TreeItemData (What))
        Keys = What.Data.keys  ()
        Keys.sort ()
        for Key in Keys:
          self.AddHierarchy (SubRoot, What.Data[Key])
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
      self.SetUpGrid (datum)
      self.TextBox.SetValue ("")
    else:
      self.ListView.DeleteAllColumns ()
      self.TextBox.SetValue ("")

  def TextChanged (self, Event):
    which = self.Hierarch.GetSelection ()
    itemdatum = self.Hierarch.GetItemData (which)
    datum = itemdatum.GetData ()
    if datum:
      pass#datum[0].Data[datum[1]] = self.TextBox.GetValue ()

  def ItemSelected (self, Event):
    # Event.GetIndex () gives us the row
    # Event.GetText () gives us the name of the first column
    print >> sys.stderr, Event.GetIndex (), Event.GetText ()

  def SetUpGrid (self, What):
    self.ListView.DeleteAllColumns ()
    self.ListView.ClearAll ()
    self.ListView.DeleteAllItems ()
    self.TextBox.SetValue ("")
    self.ItemCategory = None
    if What:
      # find the default item by it's ID
      Keys = What.Data.keys ()
      #Keys.sort ()
      if len(Keys) < 1:
        return
      if What.Data[Keys[0]].Kind == "KeyedItem":
        index = 0
        Header = []
        for Key in Keys:
          if What.Data[Key].ID.find ("Default") > 0:
            Header = What.Data[Key].Data.TopoSortKeys ()[:]
        self.ItemCategory = What # set here, b/c everything worked!
        if len(Header) < 1:
          return
        try:
          Header.remove ("description")
          Header.remove ("implementation")
        except: pass
        for Heading in Header:
          self.ListView.InsertColumn (index, Header[index])
          index += 1
        for Key in Keys:
          Item = What.Data[Key].Data
          TSKeys = Item.TopoSortKeys ()
          try:
            TSKeys.remove ("description")
            TSKeys.remove ("implementation")
          except: pass
          col = 1
          if len(Item.Data[TSKeys[0]]) > 0:
            sindex = self.ListView.InsertStringItem (index,
                                                     Item.Data[TSKeys[0]])
            TSKeys = TSKeys[1:len(TSKeys)]
            for TSKey in TSKeys:
              if TSKey in Header:
                self.ListView.SetStringItem(sindex, col, Item.Data[TSKey])
                col += 1
        for idx in xrange(index):
          self.ListView.SetColumnWidth (idx, wx.LIST_AUTOSIZE)
          if self.ListView.GetColumnWidth (idx) < 25:
            self.ListView.SetColumnWidth (idx, wx.LIST_AUTOSIZE_USEHEADER)
        

if __name__=="__main__":
  app = wx.App ()
  ArpgContent = ArpgContent (sys.argv[1])
  sf = SelectionFrame (ArpgContent)
  app.MainLoop ()
  if len(sys.argv) > 2:
    tmp = open (sys.argv[2], "w")
    print >> tmp, ArpgContent.AsXML ()
