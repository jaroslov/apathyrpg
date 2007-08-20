import wx

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
  def __init__(self):
    wx.Frame.__init__(self, None, -1, "ARPG-MS")

    self.Show (True)

if __name__=="__main__":
  app = wx.App ()
  sf = SelectionFrame ()
  app.MainLoop ()

