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
      for cdx in xrange(len(xml.childNodes)):
        child = xml.childNodes[cdx]
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

if __name__=="__main__":
  import sys
  where = "../Game/CoreRules.xml"
  if len(sys.argv) > 1:
    where = sys.argv[1]
  nt = GetNodeTable (where)
  print NodeTableToDot (nt)