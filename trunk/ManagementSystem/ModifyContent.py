#!/usr/bin/pythonw2.5

import sys
import wx
from ArpgMs import ARPG_MS as ArpgContent
import wx.grid as gridlib

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

class GridView(gridlib.Grid):
  def __init__(self, parent, id):
    gridlib.Grid.__init__(self, parent, id)

    self.WhichCategory = None
    self.WhichKeyedItems = None
    self.WhichHeadings = None
    self.WhichItem = None

    self.ItemChangedCallback = None

    self.Bind (gridlib.EVT_GRID_LABEL_RIGHT_CLICK, self.LabelRightClick)
    self.Bind (gridlib.EVT_GRID_CELL_CHANGE, self.ItemChanged)
    self.Bind (gridlib.EVT_GRID_SELECT_CELL, self.ItemSelected)

  def ClearGrid (self):
    self.WhichCategory = None
    self.WhichKeyedItems = None
    self.WhichHeadings = None
    self.WhichItem = None
    Table = self.GetTable ()
    cols = Table.GetNumberCols ()
    rows = Table.GetNumberRows ()
    if rows > 0:
      self.DeleteRows (0, rows)
    if cols > 0:
      self.DeleteCols (0, cols)

  def ItemSelected (self, Event):
    if (self.WhichCategory
        and self.WhichKeyedItems
        and self.WhichHeadings):
      self.WhichItem = (Event.GetRow (), Event.GetCol ())
      Key = self.WhichKeyedItems[Event.GetRow ()][1]
      Heading = self.WhichHeadings[Event.GetCol ()]
      KeyedItem = self.WhichCategory.Data[Key]
      Item = KeyedItem.Data
      if self.ItemChangedCallback:
        self.ItemChangedCallback (Item.Data[Heading])
    elif self.ItemChangedCallback:
      self.ItemChangedCallback ("")
    Event.Skip ()

  def ItemChanged (self, Event):
    if (self.WhichCategory
        and self.WhichKeyedItems
        and self.WhichHeadings):
      Key = self.WhichKeyedItems[Event.GetRow ()][1]
      Heading = self.WhichHeadings[Event.GetCol ()]
      KeyedItem = self.WhichCategory.Data[Key]
      Item = KeyedItem.Data
      Item.Data[Heading] = self.GetCellValue (Event.GetRow (), Event.GetCol ())
      if self.ItemChangedCallback:
        self.ItemChangedCallback (Item.Data[Heading])
      self.ContextSensitiveColoring (Item.Data[Heading], Event.GetRow (), Event.GetCol ())
    Event.Skip ()

  def LabelRightClick (self, Event):
    if Event.GetRow () > -1:
      row = Event.GetRow ()
      insertID = wx.NewId ()
      deleteID = wx.NewId ()

      if not self.GetSelectedRows ():
        self.SelectRow (row)

      menu = wx.Menu ()
      x, y = Event.GetPosition ()
      menu.Append (insertID, "Insert Row")
      menu.Append (deleteID, "Delete Row")

      def InsertRow (Event, self=self, row=row):
        if (self.WhichCategory
            and self.WhichKeyedItems
            and self.WhichHeadings):
          # find the Default
          Default = None
          for Key in self.WhichCategory.Data.keys ():
            if Key.find ("Default") > 1:
              Default = self.WhichCategory.Data[Key]
          if Default == None:
            print >> sys.stderr, "There is not Default"
            return # if there's no Default we're sunk
          NewItem = Default.Clone ()
          NewItem.ID = "__"+str(wx.NewId ())
          self.WhichCategory.Data[NewItem.ID] = NewItem
          self.SetUpGrid (self.WhichCategory)
        pass

      def DeleteRow (Event, self=self, row=row):
        if (self.WhichCategory
            and self.WhichKeyedItems
            and self.WhichHeadings):
          Key = self.WhichKeyedItems[row][1]
          del self.WhichCategory.Data[Key]
          self.SetUpGrid (self.WhichCategory)
        pass

      self.Bind (wx.EVT_MENU, InsertRow, id=insertID)
      self.Bind (wx.EVT_MENU, DeleteRow, id=deleteID)
      self.PopupMenu (menu, (x, y))
      menu.Destroy ()

  def ContextSensitiveColoring (self, Value, Row, Col):
    if (Value.find ("self >>>") >= 0
        or Value.find ("other >>>") >= 0):
      self.SetCellBackgroundColour (Row, Col, wx.Color(255,0,0))
    elif (Value.find ("[LaTeX]") >= 0
          or Value.find ("[latex]") >= 0):
      self.SetCellBackgroundColour (Row, Col, wx.Color(0,195,255))
    else:
      self.SetCellBackgroundColour (Row, Col, wx.Color(255,255,255))

  def SetUpGrid (self, What):
    self.ClearGrid ()

    Keys = What.Data.keys ()
    if len(Keys) < 1:
      return

    # check that we're gonna see leaves soon
    if "KeyedItem" != What.Data[Keys[0]].Kind:
      return

    # find the Default, sort the keys
    Default = None
    SortingKeys = []
    for Key in Keys:
      if What.Data[Key].ID.find("Default") > 1:
        Default = What.Data[Key].Data
      else:
        if What.Data[Key].Data.Data.has_key ("name"):
          SortingKeys.append ((What.Data[Key].Data.Data["name"],Key))
        else:
          SortingKeys.append ((Key,Key))
    if Default == None:
      return
    SortingKeys.sort ()

    # get the Header, remove extraneous
    Header = Default.TopoSortKeys ()[:]
    try:
      Header.remove ("implementation")
      #Header.remove ("description")
    except: pass

    self.AppendCols (len(Header))
    self.AppendRows (len(SortingKeys))

    for hdx in xrange(0,len(Header)):
      heading = Header[hdx]
      self.SetColLabelValue (hdx, heading.capitalize ())
      for skx in xrange(0,len(SortingKeys)):
        Key = SortingKeys[skx][1]
        KeyedItem = What.Data[Key]
        Item = KeyedItem.Data
        if Item.Data.has_key (heading):
          self.SetCellValue (skx, hdx, Item.Data[heading])
          self.ContextSensitiveColoring (Item.Data[heading], skx, hdx)
      if "description" != heading:
        self.AutoSizeColumn (hdx, True)

    self.WhichCategory = What
    self.WhichKeyedItems = SortingKeys
    self.WhichHeadings = Header

    ## Can't figure out how to make row-label auto-sized
    #for skx in xrange(0,len(SortingKeys)):
    #  self.GridView.SetRowLabelValue (skx, What.Data[SortingKeys[skx][1]].Data.Name)


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
      self.Bind (wx.EVT_TREE_SEL_CHANGED, self.CategorySelected, self.Hierarch)
    except:
      pass
    
    self.GridView = GridView (self.TopPanel, wx.ID_ANY)
    self.GridView.CreateGrid (0, 0)
    self.GridView.ItemChangedCallback = self.ItemChangedCallback

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
    self.TRSizer.Add (self.GridView,
                      proportion=1,
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

  def CategorySelected (self, Event):
    which = self.Hierarch.GetSelection ()
    itemdatum = self.Hierarch.GetItemData (which)
    datum = itemdatum.GetData ()
    if datum:
      self.GridView.SetUpGrid (datum)
      self.TextBox.SetValue ("")
    else:
      self.GridView.ClearGrid ()
      self.TextBox.SetValue ("")

  def TextChanged (self, Event):
    if (self.GridView.WhichCategory
        and self.GridView.WhichKeyedItems
        and self.GridView.WhichHeadings
        and self.GridView.WhichItem):
      Row = self.GridView.WhichItem[0]
      Col = self.GridView.WhichItem[1]
      self.GridView.SetCellValue (Row, Col, self.TextBox.GetValue ())
      Key = self.GridView.WhichKeyedItems[Row][1]
      Heading = self.GridView.WhichHeadings[Col]
      KeyedItem = self.GridView.WhichCategory.Data[Key]
      Item = KeyedItem.Data
      Item.Data[Heading] = self.TextBox.GetValue ()
      self.GridView.ContextSensitiveColoring (self.TextBox.GetValue (), Row, Col)
    else: pass    

  def ItemChangedCallback (self, Data):
    self.TextBox.SetValue (Data)

def Debug ():
  app = wx.App ()
  if len(sys.argv) > 1:
    ArpgContent = ArpgContent (sys.argv[1])
    sf = SelectionFrame (ArpgContent)
    app.MainLoop ()
    if len(sys.argv) > 2:
      tmp = open (sys.argv[2], "w")
      print >> tmp, ArpgContent.AsXML ()

def Live ():
  From = "ARPG-Data.xml"
  To = "ARPG-Data.xml"
  if len(sys.argv) > 1:
    From = sys.argv[1]
    To = From
  if len(sys.argv) > 2:
    To = sys.argv[2]
  app = wx.App ()
  Content = ArpgContent (From)
  sf = SelectionFrame (Content)
  app.MainLoop ()
  tmp = open (To, "w")
  print >> tmp, Content.AsXML ()

if __name__=="__main__":
  Live ()
