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

class SelectionFrame(wx.Frame):
  def __init__(self, Location=""):
    wx.Frame.__init__(self, None, wx.NewId(), "ARPG-MS",
                          pos=wx.DefaultPosition,
                          style=wx.DEFAULT_FRAME_STYLE|wx.TAB_TRAVERSAL)

    self.ArpgContent = ArpgContent (Location=Location)

    self.Hierarch = None
    self.AddHierarchy ()


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
            self.Hierarch.AppendItem (SubRoot, TSKey.capitalize ())
    else:
      self.Hierarch = wx.TreeCtrl (self)
      Root = self.Hierarch.AddRoot (self.ArpgContent.Name)
      Keys = self.ArpgContent.Data.keys ()
      Keys.sort ()
      for Key in Keys:
        self.AddHierarchy (Root, self.ArpgContent.Data[Key])

if __name__=="__main__":
  app = wx.App ()
  sf = SelectionFrame (sys.argv[1])
  app.MainLoop ()

