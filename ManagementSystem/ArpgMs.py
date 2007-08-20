class ARPG_MS(object):
  def __init__ (self, Location=None, XML=None, KeyedItem=None, Leaf=None):
    self.Kind = ""
    self.Name = ""
    self.Data = {}
    if Location:
      self.FromXMLFile (Location)
    elif XML:
      self.FromXML (XML)
    elif Leaf:
      self.FromXMLLeaf  (Leaf)
    elif KeyedItem:
      self.FromXMLKeyedItem (KeyedItem)

  def FromXMLFile (self, Location):
    import xml.dom.minidom as minix
    file = minix.parse(Location)
    self.FromXML (file.childNodes[0])

  def FromXML (self, xml):
    self.Name = xml.nodeName
    self.Kind = "Hierarch"
    if xml.hasChildNodes ():
      for child in xml.childNodes:
        if child.nodeType == xml.ELEMENT_NODE:
          if child.nodeName == "KeyedItem":
            kitem = ARPG_MS (KeyedItem=child)
            self.Data[kitem.ID] = kitem
            pass
          else:
            arpgms = ARPG_MS (XML=child)
            self.Data[arpgms.Name] = arpgms

  def FromXMLKeyedItem (self, KeyedItem):
    self.Kind = "KeyedItem"
    self.ID = KeyedItem.getAttribute("id")
    self.Name = KeyedItem.nodeName
    for child in KeyedItem.childNodes:
      if child.ELEMENT_NODE == child.nodeType:
        self.Data = ARPG_MS (Leaf=child)
        break

  def FromXMLLeaf (self, Leaf):
    self.Kind = "Leaf"
    self.Name = Leaf.nodeName
    for child in Leaf.childNodes:
      if child.nodeType == Leaf.ELEMENT_NODE:
        if child.hasChildNodes ():
          self.Data[child.nodeName] = child.childNodes[0].nodeValue
        else:
          self.Data[child.nodeName] = ""

  def AsXML (self, Indent=""):
    output = ""
    if "Hierarch" == self.Kind:
      output += Indent+"<"+self.Name+">\n"
      keys = self.Data.keys ()
      keys.sort ()
      for key in keys:
        output += self.Data[key].AsXML (Indent+"\t")+"\n"
      output += Indent+"</"+self.Name+">"
    elif "KeyedItem" == self.Kind:
      output += Indent+"<KeyedItem id=\""+self.ID+"\" >\n"
      output += self.Data.AsXML (Indent+"\t")+"\n"
      output += Indent+"</KeyedItem>"
    elif "Leaf":
      output += Indent+"<"+self.Name+">\n"
      keys = self.Data.keys ()
      keys.sort ()
      for key in keys:
        value = self.Data[key].replace ("&", "&amp;").replace("<","&lt;").replace(">","&gt;")
        output += Indent+"\t<"+key+">"+value+"</"+key+">\n"
      output += Indent+"</"+self.Name+">"
    return output
