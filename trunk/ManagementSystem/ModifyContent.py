#!/usr/bin/pythonw2.5

import sys
import wx
from ArpgMs import ArpgUni as ArpgContent
import wx.grid as gridlib
import wx.stc as stclib
import wx.html as htmllib

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
    self.Bind (gridlib.EVT_GRID_EDITOR_HIDDEN, self.EditorHides)

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
      Key = self.WhichKeyedItems[Event.GetRow ()]
      Heading = self.WhichHeadings[Event.GetCol ()]
      Datum = self.WhichCategory[Key]
      if not Datum.HasChild (Heading): # uh-oh, fix this!
        Datum[Heading] = self.WhichCategory.Default[Heading].Clone ()
      if self.ItemChangedCallback:
        self.ItemChangedCallback (Datum[Heading].Value)
    elif self.ItemChangedCallback:
      self.ItemChangedCallback ("")
    Event.Skip ()

  def ItemChanged (self, Event):
    if (self.WhichCategory
        and self.WhichKeyedItems
        and self.WhichHeadings):
      self.WhichItem = (Event.GetRow (), Event.GetCol ())
      Key = self.WhichKeyedItems[Event.GetRow ()]
      Heading = self.WhichHeadings[Event.GetCol ()]
      Datum = self.WhichCategory[Key]
      Datum[Heading].Value = self.GetCellValue (Event.GetRow (), Event.GetCol ())
      if self.ItemChangedCallback:
        self.ItemChangedCallback (Datum[Heading].Value)
      self.ContextSensitiveColoring (Datum[Heading].Value, Event.GetRow (), Event.GetCol ())
    Event.Skip ()

  def EditorHides (self, Event):
    pass

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
          Default = self.WhichCategory.Default
          if Default == None:
            print >> sys.stderr, "There is no Default"
            return # if there's no Default we're sunk
          NewItem = Default.Clone ()
          NewItem.Kind = "datum"
          NewItem.Name = "__"+str(wx.NewId ())
          self.WhichCategory[NewItem.Name] = NewItem
          self.SetUpGrid (self.WhichCategory)
        pass

      def DeleteRow (Event, self=self, row=row):
        if (self.WhichCategory
            and self.WhichKeyedItems
            and self.WhichHeadings):
          Key = self.WhichKeyedItems[row]
          del self.WhichCategory[Key]
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

    Keys = What.keys ()
    if len(Keys) < 1:
      return

    # check that we're gonna see leaves soon
    if "datum" != What[Keys[0]].Kind:
      return

    # find the Default, sort the keys
    Default = What.Default
    DataKeys = What.keys ()

    # get the Header, remove extraneous
    Header = Default.keys ()
    try:
      Header.remove ("implementation")
      #Header.remove ("description")
    except: pass

    self.AppendCols (len(Header))
    self.AppendRows (len(DataKeys))

    for hdx in xrange(0,len(Header)):
      heading = Header[hdx]
      self.SetColLabelValue (hdx, heading.capitalize ())
      for skx in xrange(0,len(DataKeys)):
        Key = DataKeys[skx]
        Datum = What[Key]
        if Datum.HasChild (heading):
          self.SetCellValue (skx, hdx, Datum[heading].Value)
          self.ContextSensitiveColoring (Datum[heading].Value, skx, hdx)
      if "description" != heading:
        self.AutoSizeColumn (hdx, True)

    self.WhichCategory = What
    self.WhichKeyedItems = DataKeys
    self.WhichHeadings = Header

    ## Can't figure out how to make row-label auto-sized
    #for skx in xrange(0,len(SortingKeys)):
    #  self.GridView.SetRowLabelValue (skx, What.Data[SortingKeys[skx][1]].Data.Name)

class HtmlBox(wx.Notebook):
  def __init__ (self, parent, id=wx.ID_ANY):
    wx.Notebook.__init__ (self, parent, id, style=wx.NB_TOP)
    self.HtmlWindow = htmllib.HtmlWindow (self)
    self.Editor = stclib.StyledTextCtrl (self)

    #print >> sys.stderr, dir(stclib)

    self.Bind(wx.EVT_NOTEBOOK_PAGE_CHANGED, self.OnPageChanged)

    self.AddPage (self.HtmlWindow, "Render")
    self.AddPage (self.Editor, "Edit")

    self.Constants = {stclib.STC_H_ASP:"",
                      stclib.STC_H_ASPAT:"",
                      stclib.STC_H_ATTRIBUTE:"",
                      stclib.STC_H_ATTRIBUTEUNKNOWN:"",
                      stclib.STC_H_CDATA:"fore:#FFFFFF,back:#000000",
                      stclib.STC_H_COMMENT:"fore:#339966",
                      stclib.STC_H_DEFAULT:"",
                      stclib.STC_H_DOUBLESTRING:"",
                      stclib.STC_H_ENTITY:"fore:#00DD00",
                      stclib.STC_H_NUMBER:"fore:#FF0000",
                      stclib.STC_H_OTHER:"",
                      stclib.STC_H_QUESTION:"fore:#00DD66",
                      stclib.STC_H_SCRIPT:"",
                      stclib.STC_H_SGML_1ST_PARAM:"",
                      stclib.STC_H_SGML_1ST_PARAM_COMMENT:"",
                      stclib.STC_H_SGML_BLOCK_DEFAULT:"",
                      stclib.STC_H_SGML_COMMAND:"",
                      stclib.STC_H_SGML_COMMENT:"",
                      stclib.STC_H_SGML_DEFAULT:"",
                      stclib.STC_H_SGML_DOUBLESTRING:"",
                      stclib.STC_H_SGML_ENTITY:"",
                      stclib.STC_H_SGML_ERROR:"",
                      stclib.STC_H_SGML_SIMPLESTRING:"",
                      stclib.STC_H_SGML_SPECIAL:"",
                      stclib.STC_H_SINGLESTRING:"",
                      stclib.STC_H_TAG:"fore:#993399",
                      stclib.STC_H_TAGEND:"fore:#996666",
                      stclib.STC_H_TAGUNKNOWN:"fore:#669900",
                      stclib.STC_H_VALUE:"fore:000000",
                      stclib.STC_H_XCCOMMENT:"",
                      stclib.STC_H_XMLEND:"",
                      stclib.STC_H_XMLSTART:""}

    self.SetUpHtmlStyle ()

  def SetUpHtmlStyle (self):
    self.Editor.SetLexer (stclib.STC_LEX_PYTHON)
    try:
      for key in self.Constants.keys ():
        try:
          pass#self.Editor.StyleSetSpec (key, self.Constants[key])
        except:
          print >> sys.stderr, key, self.Constants[key]
    except:
      print >> sys.stderr, self.Constants

  def SetValue (self, String):
    self.Editor.SetText (String)
    self.HtmlWindow.SetPage (self.GetValue ())
  def GetValue (self):
    return self.Editor.GetText ()
  def OnPageChanged (self, Event):
    self.HtmlWindow.SetPage (self.GetValue ())
    Event.Skip ()

class SelectionFrame(wx.Frame):
  def __init__(self, ArpgContent):
    wx.Frame.__init__(self, None, wx.NewId(), "ARPG-MS",
                      pos=wx.DefaultPosition,
                      size=(800,600),
                      style=wx.DEFAULT_FRAME_STYLE|wx.FRAME_EX_METAL|wx.TAB_TRAVERSAL)

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

    self.RenderBox = HtmlBox (self.BottomPanel)
    #  self.RenderBox = wx.TextCtrl (self.BottomPanel,
    #                              style=wx.TE_WORDWRAP|wx.TE_MULTILINE)
    self.Bind (stclib.EVT_STC_CHANGE, self.TextChanged, self.RenderBox.Editor)

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
    self.BRSizer.Add (self.RenderBox,
                      proportion=1,
                      flag=wx.EXPAND
                      | wx.ALL
                      | wx.ALIGN_CENTER_HORIZONTAL
                      | wx.ALIGN_CENTER_VERTICAL,
                      border=5)
    self.BottomPanel.SetSizer (self.BRSizer)

    self.RightSplitter.SplitHorizontally (self.TopPanel, self.BottomPanel, -200)
    self.RightSplitter.SetSashGravity (.9)
    self.MainSplitter.SplitVertically (self.LeftPanel, self.RightSplitter, -600)
    self.MainSplitter.SetSashGravity (.1)
    
    self.Show (True)

  def AddHierarchy (self, Root=None, What=None):
    if self.Hierarch:
      if "category" == What.Kind:
        Name = What.Name
        SubRoot = self.Hierarch.AppendItem (Root, Name,
                                            data=wx.TreeItemData (What))
        Keys = What.keys  ()
        for Key in Keys:
          self.AddHierarchy (SubRoot, What[Key])
    else:
      self.Hierarch = wx.TreeCtrl (self.LeftPanel)
      Root = self.Hierarch.AddRoot (self.ArpgContent.Name)
      Keys = self.ArpgContent.keys ()
      for Key in Keys:
        self.AddHierarchy (Root, self.ArpgContent[Key])
      self.Hierarch.Expand (Root)

  def CategorySelected (self, Event):
    which = self.Hierarch.GetSelection ()
    itemdatum = self.Hierarch.GetItemData (which)
    datum = itemdatum.GetData ()
    if datum:
      self.GridView.SetUpGrid (datum)
      self.RenderBox.SetValue ("")
    else:
      self.GridView.ClearGrid ()
      self.RenderBox.SetValue ("")

  def TextChanged (self, Event):
    if (self.GridView.WhichCategory
        and self.GridView.WhichKeyedItems
        and self.GridView.WhichHeadings
        and self.GridView.WhichItem):
      Row = self.GridView.WhichItem[0]
      Col = self.GridView.WhichItem[1]
      self.GridView.SetCellValue (Row, Col, self.RenderBox.GetValue ())
      Key = self.GridView.WhichKeyedItems[Row]
      Heading = self.GridView.WhichHeadings[Col]
      Datum = self.GridView.WhichCategory[Key]
      Datum[Heading].Value = self.RenderBox.GetValue ()
      self.GridView.ContextSensitiveColoring (self.RenderBox.GetValue (), Row, Col)
    else: pass    

  def ItemChangedCallback (self, Data):
    self.RenderBox.SetValue (Data)

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
  print >> tmp, Content.AsXml ()

if __name__=="__main__":
  Live ()
