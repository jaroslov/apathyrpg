TopoImportance = ["name",
                  "metatag",
                  "class",
                  "type",
                  "rank",
                  "level",
                  "attribute",
                  "casting_time",
                  "cost",
                  "note",
                  "encumbrance",
                  "piercing",
                  "slashing",
                  "crushing",
                  "damage",
                  "hands",
                  "minimum_strength",
                  "maximum_strength_bonus",
                  "recovery",
                  "duration",
                  "magic_points",
                  "range",
                  "target",
                  "occupation",
                  "time",
                  "skills",
                  "items",
                  "spells",
                  "traits",
                  "special_abilities",
                  "statistics",
                  "attacks",
                  "slots",
                  "requirements",
                  "character_points",
                  "description",
                  "implementation"]

class ARPG_MS(object):
  def __init__ (self, Location=None, XML=None, KeyedItem=None, Leaf=None):
    self.Kind = ""
    self.Name = ""
    self.Data = {}
    self.ID = ""
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
    if xml.hasAttributes ():
      self.ID = xml.getAttribute("id")
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

  def Clone (self):
    Cp = ARPG_MS ()
    Cp.Kind = self.Kind
    Cp.Name = self.Name
    Cp.ID   = self.ID
    if "Hierarch" == self.Kind:
      for key in self.Data.keys ():
        Cp.Data[key] = self.Data[key].Clone ()
    elif "KeyedItem" == self.Kind:
      Cp.Data = self.Data.Clone ()
    elif "Leaf" == self.Kind:
      for key in self.Data.keys ():
        Cp.Data[key] = "" + self.Data[key]
    return Cp

  def LeftMerge (self, Other):
    # merges together...
    if self.Kind == Other.Kind:
      if self.Name != Other.Name:
        self.Name += "__COLLISION__" + Other.Name
      if self.ID != Other.ID:
        self.ID += "__COLLISION__" + Other.ID
      if "Hierarch" == self.Kind or "Leaf" == self.Kind:
        CommonKeys = {}
        for key in self.Data.keys ():
          CommonKeys[key] = None
        for key in Other.Data.keys ():
          CommonKeys[key] = None
        if "Hierarch" == self.Kind:
          for key in CommonKeys.keys ():
            if self.Data.has_key (key) and Other.Data.has_key (key):
              self.Data[key].LeftMerge (Other.Data[key])
            elif Other.Data.has_key (key):
              self.Data[key] = Other.Data[key].Clone ()
        else:
          for key in CommonKeys.keys ():
            if self.Data.has_key (key) and Other.Data.has_key (key):
              if self.Data[key] != Other.Data[key]:
                self.Data[key] = "self >>>\n"+self.Data[key]+"\nother >>>\n"+Other.Data[key]
            elif Other.Data.has_key (key):
              self.Data[key] = ""+Other.Data[key]
      elif "KeyedItem" == self.Kind:
        self.Data.LeftMerge (Other.Data)
    else: pass # mismatch, returns error

  def Merge (self, Other):
    Cp = self.Clone ()
    Cp.LeftMerge (Other)
    return Cp

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
          self.Data[child.nodeName].replace("&lt;","<").replace("&gt;",">").replace("&amp;","&")
        else:
          self.Data[child.nodeName] = ""

  def AsXML (self, Indent=""):
    output = ""
    if "Hierarch" == self.Kind:
      output += Indent+"<"+self.Name+" id=\""+self.ID+"\" >\n"
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

  def TopoSortKeys (self):
    zkeys = self.Data.keys()
    topokeys = []
    for ikey in TopoImportance:
      if ikey in zkeys:
        topokeys.append(ikey)
    for zkey in zkeys:
      if zkey not in topokeys:
        topokeys.append(zkey)
    return topokeys

def TestMerge ():
  C1 = ARPG_MS (Location="../Game/ARPG-Data.xml")
  C2 = ARPG_MS (Location="../Game/ARPG-Data2.xml")
  C3 = C1.Merge (C2)
  out = open ("LMerge.xml", "w")
  print >> out, C3.AsXML ()

if __name__=="__main__":
  TestMerge ()
