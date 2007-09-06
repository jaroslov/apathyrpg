#
# What's going on?
#  Right now getxsd does a two-node analysis, and poorly at that;
#  the correct thing to do is to do subgraph unions for all 
#  subgraphs starting from the same tree-like node.

class MergingNode (object):
  def __init__ (self, xml=None):
    self.Name = ""
    self.Children = []
    self.ChildPositions = {}
    self.Attributes = {}
    if xml is not None:
      self.Name = xml.nodeName
      if xml.hasChildNodes ():
        for child in xml.childNodes:
          self.Children.append (MergingNode (child))
      if xml.attributes is not None:
        for attr in xml.attributes.keys ():
          self.Attributes[attr] = xml.getAttribute (attr)
  def __str__ (self):
    return self.ToString ()
  def ToString (self, Indent="", NamePre=""):
    res = Indent+self.Name + NamePre + ":"
    for attr in self.Attributes.keys ():
      res += " " + attr
    for child in self.Children:
      namepre = ""
      if child.Name in self.ChildPositions:
        namepre = "[" + str(self.ChildPositions[child.Name]) + "]"
      res += "\n"+Indent+child.ToString (Indent+" ", namepre)
    return res

def KMerge (MNodes):
  # performs a k-way merge of MergingNodes, recursively
  if len(MNodes) < 1:
    return
  OutPut = MergingNode ()
  # Name
  OutPut.Name = MNodes[0].Name
  # Merge attributes
  #  1) add unique attribute names
  #  2) and only unique attribute values
  for mnode in MNodes:
    for attr in mnode.Attributes.keys ():
      if attr not in OutPut.Attributes: # unique names
        OutPut.Attributes[attr] = [mnode.Attributes[attr]]
      else: # unique values
        if mnode.Attributes[attr] not in OutPut.Attributes[attr]:
          OutPut.Attributes[attr].append (mnode.Attributes[attr])
  # Merge children
  #  1) keep track of the index of the children
  #  2) merge the children, recursively & by kind
  # First, let's recursively merge the children
  childLists = {}
  childPositions = {}
  for mnode in MNodes:
    index = 0
    for child in mnode.Children:
      if child.Name not in childLists:
        childLists[child.Name] = []
        childPositions[child.Name] = {}
      if child.Name[0] != "#":
        if index not in childPositions[child.Name]:
          childPositions[child.Name][index] = 1
        else:
          childPositions[child.Name][index] += 1
        index += 1
      childLists[child.Name].append (child)
  for ckey in childLists.keys ():
    mchild = KMerge (childLists[ckey])
    OutPut.Children.append (mchild)
  OutPut.ChildPositions = childPositions
  return OutPut

def GetAllNodeKinds (filename=None, xml=None, NodeKinds=None):
  if filename is not None:
    NodeKinds = {}
    import xml.dom.minidom as minix
    xml = minix.parse(filename)
    GetAllNodeKinds (None, xml, NodeKinds)
  else:
    if xml.nodeName not in NodeKinds:
      NodeKinds[xml.nodeName] = [MergingNode (xml)]
    else:
      NodeKinds[xml.nodeName].append (MergingNode (xml))
    if xml.hasChildNodes:
      for child in xml.childNodes:
        GetAllNodeKinds (None, child, NodeKinds)
  return NodeKinds

class Node(object):
  def __init__ (self):
    self.Name = ""
    self.Children = {}
    self.Attributes = []

class TableEntry(object):
  def __init__ (self, Ex=False):
    self.Exists = Ex
    self.Attributes = []
    self.Unique = True
    self.Positions = []
    self.rPositions = []
  def __str__ (self):
    if self.Exists:
      unique = "!"
      if not self.Unique:
        unique = "*"
      return unique+str(self.Attributes)
    return ""

def GetNodeKinds (filename=None,xml=None,NodeKinds=None):
  if filename is not None:
    NodeKinds = {}
    import xml.dom.minidom as minix
    xml = minix.parse(filename)
    GetNodeKinds (None, xml, NodeKinds)
  else:
    if xml.nodeName not in NodeKinds:
      NodeKinds[xml.nodeName] = ""
    if xml.hasChildNodes:
      for child in xml.childNodes:
        GetNodeKinds (None, child, NodeKinds)
  return NodeKinds

def GetNodeTable (filename, xml=None, NodeTable=None):
  if filename is not None:
    NodeTable = {}
    import xml.dom.minidom as minix
    xml = minix.parse(filename)
    NodeKinds = GetNodeKinds (None, xml, {})
    for key in NodeKinds.keys ():
      NodeTable[key] = {}
      for skey in NodeKinds.keys ():
        NodeTable[key][skey] = TableEntry ()
    GetNodeTable (None, xml, NodeTable)
  else:
    rowN = xml.nodeName
    if xml.hasChildNodes ():
      ckind = {}
      cdx = 0
      for child in xml.childNodes:
        if child.nodeType == child.ELEMENT_NODE:
          colN = child.nodeName
          if colN in ckind:
            ckind[colN] += 1
          else:
            ckind[colN] = 1
  
          NodeTable[rowN][colN].Exists = True
          if cdx not in NodeTable[rowN][colN].Positions:
            NodeTable[rowN][colN].Positions.append (cdx)
          rcdx = len(xml.childNodes) - cdx - 1
          if rcdx not in NodeTable[rowN][colN].rPositions:
            NodeTable[rowN][colN].rPositions.append (rcdx)
          if child.attributes:
            for attr in child.attributes.keys ():
              if attr not in NodeTable[rowN][colN].Attributes:
                NodeTable[rowN][colN].Attributes.append (attr)
          GetNodeTable (None, child, NodeTable)
          cdx += 1
        elif child.nodeType == child.TEXT_NODE:
          value = ""
          if child.nodeValue is not None:
            value = child.nodeValue.strip ("\t\n\v\f ")
          if len(value) > 0:
            NodeTable[rowN][child.nodeName].Exists = True
      for ckey in ckind.keys ():
        if ckind[ckey] > 1:
          NodeTable[rowN][ckey].Unique = False
  return NodeTable

def NodeTableToXsd (NodeTable):
  tab = "  "
  res = ""
  res += '<?xml version "1.0" encoding="UTF-8" ?>\n'
  res += '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">\n'
  # write down all of the elements as types
  res += "\n<!-- Element Declarations -->\n\n"
  keys = NodeTable.keys ()
  keys.sort ()
  for key in keys:
    if key[0] != "#":
      res += tab + '<xs:element name="'+key+'" type="'+key+'Type" />\n'
  res += "\n<!-- Element Definitions -->\n\n"
  for row in keys:
    if row[0] == "#":
      continue
    # a row represents a possible parent element
    # where each column are possible children elements
    # (1) if a row has no children but #text, it is
    #     just an 'xs:string' type
    #     otherwise it is *actually* complex
    # (2) nontrivial children are placed into the container
    #     childrenTypes; if the Position or rPosition is fixed
    #     then this is marked as well
    onlyHashText = True
    serialChildren = {}
    fixedPosition = {} # known to occur before other riffraff
    #fixedrPosition = {} # we'll deal with fixedrPosition later
    import sys
    print >> sys.stderr, row,
    for col in keys:
      if NodeTable[row][col].Exists:
        print >> sys.stderr, col, NodeTable[row][col].Positions,
      if col[0] != "#" and NodeTable[row][col].Exists:
        onlyHashText = False
        node = NodeTable[row][col]
        if len(node.Positions) == 1:
          fixedPosition[node.Positions[0]] = col
        else:
          serialChildren[col] = node
    print >> sys.stderr
    # now count fixedPosition and throw away non-serial positions
    tmpFPs = {}
    for idx in xrange(len(fixedPosition)):
      if idx in fixedPosition:
        tmpFPs[idx] = fixedPosition[idx]
    for idx in tmpFPs.keys ():
      if idx not in fixedPosition.keys ():
        serialChildren[fixedPosition[idx]] = NodeTable[row][fixedPosition[idx]]
    fixedPosition = tmpFPs
    # simpleTypes (text)
    if onlyHashText:
      res += tab + '<xs:simpleType name="'+row+'Type">\n'
      res += tab*2 + '<xs:restriction base="xs:string" />\n'
      res += tab + '</xs:simpleType>\n\n'
    # complexTypes (has children)
    # fixed portion
    else:
      res += tab + '<xs:complexType name="'+row+'Type" >\n'
      fixedkeys = fixedPosition.keys ()
      fixedkeys.sort ()
      for fixedkey in fixedkeys:
        col = fixedPosition[fixedkey]
        res += tab*2 + '<xs:element name="'+col+'" type="'+col+'Type" />\n'
      res += tab*2 + '<xs:sequence>\n'
      res += tab*3 + '<xs:choice>\n'
      scKeys = serialChildren.keys ()
      scKeys.sort ()
      for schld in scKeys:
        res += tab*4 + '<xs:element name="'+schld+'" type="'+schld+'Type" />\n'
      res += tab*3 + '</xs:choice>\n'      
      res += tab*2 + '</xs:sequence>\n'      
      res += tab + '</xs:complexType>\n\n'
  res += '</xs:schema>'
  return res

def NodeTableToDot (NodeTable):
  dot = "digraph G {\n\tcompound=true;\n"
  dot += "\tsubgraph clusterAttributes {\n"
  attributes = []
  for col in NodeTable.keys ():
    for row in NodeTable.keys ():
      for attr in NodeTable[col][row].Attributes:
        if attr not in attributes:
          attributes.append (attr)
  for attr in attributes:
    dot += "\t\t\"" + attr + "\";\n"
  dot += "\t\t"
  for adx in xrange(len(attributes)):
    dot += "\""+attributes[adx]+"\""
    if (adx+1) < len(attributes):
      dot += " -> "
    else:
      dot += " [style=invis];\n"
  dot += "\t}\n"
  for col in NodeTable.keys ():
    prCol = col
    shape = "[shape=ellipse]"
    if '#' == prCol[0]:
      shape = "[shape=box]"
    prCol = "\"" + col + "\""
    dot += "\t" + prCol + " " + shape + ";\n"
    for row in NodeTable.keys ():
      if NodeTable[col][row].Exists:
        prRow = "\""+row+"\""
        dot += "\t" + prCol + " -> " + prRow + ";\n"
  for col in NodeTable.keys ():
    prCol = "\""+col+"\""
    for row in NodeTable.keys ():
      if NodeTable[col][row].Exists:
        prRow = "\""+row+"\""
        for attr in NodeTable[col][row].Attributes:
          dot += "\t" + prRow + " -> " + "\"" + attr + "\"" + " [color=blue,weight=.1];\n"
  dot += "}\n"
  return dot

def PrintNodeTableNicely (NodeTable):
  res = ""
  keys = NodeTable.keys ()
  keys.sort ()
  res += " "*12
  values = []
  for key in keys:
    res += "%-12s"%key
  res += "\n"
  for key in keys:
    res += "%12s"%key
    for skey in keys:
      res += "%-12s"%str(NodeTable[key][skey])
    res += "\n"
  return res

if __name__=="__main__":
  import sys
  where = "../Game/CoreRules.xml"
  if len(sys.argv) > 1:
    where = sys.argv[1]
  nt = GetAllNodeKinds (where)
  ntkeys = nt.keys ()
  ntkeys.sort ()
  ntnew = {}
  for ntkey in ntkeys:
    km = KMerge (nt[ntkey])
    ntnew[km.Name] = km
  #  print km
  print ntnew["#document"]
  #nt = GetNodeTable (where)
  #print PrintNodeTableNicely (nt)
  #print NodeTableToDot (nt)
  #print NodeTableToXsd (nt)