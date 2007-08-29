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
      for child in xml.childNodes:
        if child.nodeType == child.ELEMENT_NODE:
          colN = child.nodeName
          if colN in ckind:
            ckind[colN] += 1
          else:
            ckind[colN] = 1
  
          NodeTable[rowN][colN].Exists = True
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

def GetInfo (filename=None,xml=None,NodeKinds=None):
  if filename is not None:
    NodeKinds = {}
    import xml.dom.minidom as minix
    xml = minix.parse(filename)
    GetInfo (None, xml, NodeKinds)
  else:
    if xml.nodeName in NodeKinds:
      node = NodeKinds[xml.nodeName]
    else:
      NodeKinds[xml.nodeName] = Node ()
      node = NodeKinds[xml.nodeName]
      node.Name = xml.nodeName
    if xml.hasChildNodes:
      ckind = {}
      for child in xml.childNodes:

        if child.nodeName in ckind:
          ckind[child.nodeName] += 1
        else:
          ckind[child.nodeName] = 1

        if child.nodeName in node.Children:
          info = node.Children[child.nodeName]
          if ckind[child.nodeName] > 1:
            node.Children[child.nodeName] = "sequence"
        else:
          node.Children[child.nodeName] = "unique"
        GetInfo (None, child, NodeKinds)
    if xml.attributes is not None:
      for attr in xml.attributes.keys ():
        if attr in node.Attributes:
          pass
        else:
          node.Attributes.append (attr)
  return NodeKinds

if __name__=="__main__":
  import sys
  where = "../Game/CoreRules.xml"
  if len(sys.argv) > 1:
    where = sys.argv[1]
  nt = GetNodeTable (where)
  keys = nt.keys ()
  keys.sort ()
  print " "*12,
  for key in keys:
    print "%-12s"%key,
  print
  for key in keys:
    print "%12s"%key,
    for skey in keys:
      print "%-12s"%str(nt[key][skey]),
    print