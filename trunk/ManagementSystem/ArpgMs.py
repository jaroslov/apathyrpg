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
                  "prerequisite",
                  "cp",
                  "location",
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

  def UniformXml (self, Indent="", Default=False):
    # CategoryRule ::= (CategoryRule*)|(DefaultRule DatumRule*)
    # DefaultRule ::= FieldRule*
    # DatumRule ::= FieldRule*
    #
    # CategoryRule => <category name="...">?</category>
    # DefaultRule => <default name="...">?</default>
    # DatumRule => <datum name="...">?</datum>
    # FieldRule => <field name="...">?</field>
    output = ""
    if "Hierarch" == self.Kind:
      output += Indent+"<category name=\""+self.Name+"\">\n"
      keys = self.Data.keys ()
      keys.sort ()
      for key in keys:
        output += self.Data[key].UniformXml (Indent+"\t")+"\n"
      output += Indent+"</category>"
    elif "KeyedItem" == self.Kind:
      if self.ID.find ("Default") > 0:
        output += self.Data.UniformXml (Indent+"\t", True)
      else:
        output += self.Data.UniformXml (Indent+"\t")
    elif "Leaf":
      name = "datum"
      if Default: name = "default"
      output += Indent+"<"+name+" name=\""+self.Data["name"].replace("\"","\\\"")+"\" >\n"
      keys = self.Data.keys ()
      keys.sort ()
      for key in keys:
        value = self.Data[key].replace ("&", "&amp;").replace("<","&lt;").replace(">","&gt;")
        output += Indent+"\t<field name=\""+key+"\" "
        if len(value) > 0:
          output += ">"+value+"</field>\n"
        else: output += "/>\n"
      output += Indent+"</"+name+">"
    return output

class ArpgUni(object):
  def __init__ (self, Location=None):
    self.Name = "" # attribute of `name="..."'
    self.Kind = "" # category, default, rule, field
    self.Value = ""
    self.Default = None
    self._Children = {}
    if Location:
      self.FromXmlFile (Location)

  def FromXmlFile (self, Location):
    import xml.dom.minidom as minix
    file = minix.parse(Location)
    # the first node is the "document" node, ignore
    self.FromXml (file.childNodes[0])

  def FromXml (self, xml):
    import sys
    self.Kind = xml.nodeName
    if xml.hasAttributes ():
      self.Name = xml.getAttribute("name")
    if xml.hasChildNodes ():
      for child in xml.childNodes:
        if child.nodeType == xml.ELEMENT_NODE:
          uchld = ArpgUni ()
          uchld.FromXml (child)
          if "default" == uchld.Kind:
            self.Default = uchld
          else:
            self._Children[uchld.Name] = uchld
        else:
          self.Value += self.DexmlizeString(child.nodeValue)

  def XmlizeString (self, String):
    return String.replace("&","&amp;").replace("<","&lt;").replace(">","&gt;").replace("\"","&quot;").replace("\'","&apos;");

  def DexmlizeString (self, String):
    return String.replace("&amp;","&").replace("&lt;","<").replace("&gt;",">").replace("&quot;","\"").replace("&apos;","\'")

  def HasChild (self, Kid):
    return self._Children.has_key (Kid)

  def AsXml (self, Indent=""):
    output = Indent + "<" + self.Kind + " name=\"" + self.XmlizeString (self.Name) + "\""
    if "field" == self.Kind:
      if len(self.Value) > 0:
        val = self.XmlizeString (self.Value)
        output += " >" + val + "</" + self.Kind + ">"
      else: output += " />"
    else:
      output += " >\n"
      if self.Default:
        output += self.Default.AsXml (Indent+"\t")+"\n"
      for child in self.keys ():
        output += self[child].AsXml (Indent+"\t")+"\n"
      output += Indent + "</" + self.Kind + ">"
    return output

  def __getitem__ (self, key):
    return self._Children[key]

  def __setitem__ (self, key, value):
    self._Children[key] = value

  def __delitem__ (self, key):
    del self._Children[key]

  def __len__ (self):
    return len(self._Children)

  def keys (self):
    if "category" != self.Kind:
      zkeys = self._Children.keys()
      topokeys = []
      for ikey in TopoImportance:
        if ikey in zkeys:
          topokeys.append(ikey)
      for zkey in zkeys:
        if zkey not in topokeys:
          topokeys.append(zkey)
      return topokeys
    else:
      keys = self._Children.keys ()
      keys.sort ()
      return keys

  def WalkStructures (self, Other, Walker):
    if self.Kind == Other.Kind:
      Walker.SimilarKind (self, Other)
      for key in self.keys ():
        if Other.HasChild (key):
          Walker.BothHaveChild (self, Other, key)
          self[key].WalkStructures (Other[key], Walker)
        else:
          Walker.OtherDoesntHaveChild (self, Other, key)
    else:
      Walker.DissimilarKind (self, Other)

  def StructuralEquivalence (self, Other, FieldEq = False):
    class Walker(object):
      def __init__ (self):
        self.Equivalent = True
        self.FieldEq = FieldEq
      def SimilarKind (self, Left, Right):
        if "field" == Left.Kind and self.FieldEq:
          self.Equivalent = (self.Equivalent and Left.Value == Right.Value)
      def BothHaveChild (self, Left, Right, key):
        pass
      def OtherDoesntHaveChild (self, Left, Right, key):
        self.Equivalent = False
      def DissimlarKind (self, Left, Right):
        self.Equivalent = False
    Walk = Walker ()
    self.WalkStructures (Other, Walk)
    return Walk.Equivalent

  def Clone (self):
    Other = ArpgUni ()
    Other.Kind = self.Kind
    Other.Name = self.Name
    if self.Default:
      Other.Default = self.Default.Clone ()
    for key in self.keys ():
      Other[key] = self[key].Clone ()
    return Other

def MakeHtmlTable (Category, Caption=""):
  """
  Makes an HTML-formatted Table from a given category. Non-recursive.
  Note, items are placed in the Table if the Default item in the category
  contains the word "Table" in it's text.
  """
  if not Category.Default:
    return ""
  Headings = []
  for field in Category.Default.keys (): # look for the word "Table"
    if Category.Default[field].Value.find ("Table") >= 0:
      Headings.append (field)
  # begin the table
  output = "<table class=\"database-table\" id=\""+Category.Name+"\">"
  output += "\n\t<caption class=\"database-table-caption\">"+Caption+"</caption>\n"
  # table heading
  output += "\t<thead class=\"database-table-head\"><tr>"
  for Heading in Headings:
    output += "<td>"+Heading+"</td>"
  output += "</tr></thead>\n"
  # table body
  output += "\t<tbody class=\"database-table-body\">\n"
  for key in Category.keys ():
    output += "\t\t<tr>"
    for Heading in Headings:
      Value = ""
      if Category[key].HasChild (Heading):
        Value = Category[key][Heading].Value
      output += "<td>"+Value+"</td>"
    output += "</tr>\n"
  output += "\t</tbody>\n"
  # end the table
  output += "</table>"
  return output

def MakeFormattedDescription (Datum, Title, DescTbl, Description):
  """
  A div with a title selected by "Title" a small, two-column table
  generated from DescTbl and a paragraph from "Description"
  """
  # start the div
  output = "<div class=\"database-description\">\n"
  # heading
  output += "\t<h1>"+Datum[Title].Value+"</h1>\n"
  # table
  output += "\t<table>\n"
  # table is two-column: find length of left & right columns
  # insertion items: field-name, field-value, ...
  tblitems = []
  for dsc in DescTbl:
    tblitems.append (dsc)
    if Datum.HasChild (dsc):
      tblitems.append (Datum[dsc].Value)
    else:
      tblitems.append ("")
  llen = len(DescTbl) / 2
  rlen = len(DescTbl) - llen
  if llen < rlen:
    tmp = llen
    llen = rlen
    rlen = tmp
    tblitems.append ("")
    tblitems.append ("")
  fnmcls = "database-description-table-field-name"
  fvlcls = "database-description-table-field-value"
  for idx in xrange(llen):
    output += "\t\t<tr><td class=\""+fnmcls+"\">"+tblitems[idx*4]+":</td>"
    output += "<td class=\""+fvlcls+"\">"+tblitems[idx*4+1]+"</td>"
    output += "<td class=\""+fnmcls+"\">"+tblitems[idx*4+2]+":</td>"
    output += "<td class=\""+fvlcls+"\">"+tblitems[idx*4+3]+"</td></tr>\n"
  output += "\t</table>\n"
  # description
  output += "\t<p>"+Datum[Description].Value+"</p>\n"
  # end the div
  output += "</div>"
  return output

def MakeFormattedDescriptions (Category):
  """
  Makes an HTML-formatted description. This consists of a <div> element
  that contains a title, possibly a small table, and then a longer
  description.
  The following keys are marked in the "Default" item:
  "title" is marked "Title", the last one marked is used
  "table-items" are marked "DescTbl"
  "description" is marked "Description"
  """
  if not Category.Default:
    return ""
  Title = ""
  DescTbl = []
  Description = ""
  for field in Category.Default.keys ():
    if Category.Default[field].Value.find ("Title") >= 0:
      Title = field
    if Category.Default[field].Value.find ("DescTbl") >= 0:
      DescTbl.append (field)
    if Category.Default[field].Value.find ("Description") >= 0:
      Description = field
  output = ""
  for key in Category.keys ():
    output += MakeFormattedDescription  (Category[key], Title, DescTbl, Description) + "\n"
  return output

def MakeHtmlCategory (Category, Caption=""):
  output = MakeHtmlTable (Category)+"\n"
  output += MakeFormattedDescriptions (Category)
  return output

def TestMerge ():
  C1 = ARPG_MS (Location="UniData.xml")
  C2 = ARPG_MS (Location="RTUData.xml")
  C3 = C1.Merge (C2)
  out = open ("LMerge.xml", "w")
  print >> out, C3.AsXML ()

def TestUni ():
  C1 = ArpgUni (Location="UniData.xml")
  out = open("RTUData.xml", "w")
  print >> out, C1.AsXml ()

def TestStructuralEq ():
  C1 = ArpgUni (Location="UniData.xml");
  C2 = ArpgUni (Location="RTUData.xml")
  print C1.StructuralEquivalence (C1), C1.StructuralEquivalence (C2), C1.StructuralEquivalence (C2, True)

def TestConvert ():
  C1 = ARPG_MS (Location="../Game/ARPG-Data.xml")
  out = open ("UniData.xml","w")
  print >> out, C1.UniformXml ()

def TestRoundtrip ():
  C1 = ArpgUni (Location="RTUData.xml")
  out = open ("RTUData.xml", "w")
  print >> out, C1.AsXml ()

def TestOpen ():
  C1 = ArpgUni (Location="blah.xml")

def TestMakeTable ():
  C1 = ArpgUni (Location="UniData.xml")
  print MakeHtmlTable (C1["Academic"],"Auto-Generated Table")

def TestMakeFormattedDescs ():
  C1 = ArpgUni (Location="UniData.xml")
  print MakeFormattedDescriptions (C1["Magic"]["Air"])

def TestMakeHtmlCategory ():
  C1 = ArpgUni (Location="UniData.xml")
  print MakeHtmlCategory (C1["Magic"]["Air"])

if __name__=="__main__":
  #TestOpen ()
  #TestUni ()
  #TestConvert ()
  #TestStructuralEq ()
  #TestRoundtrip ()
  #TestMakeTable ()
  #TestMakeFormattedDescs ()
  TestMakeHtmlCategory ()
