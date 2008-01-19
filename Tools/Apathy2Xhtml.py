from xml.dom.minidom import parse as xmlParser

def html2general(Node):
  ## really, we could get anything
  ## however, this is the mapping we want:
  #
  ## li               -> item
  ## ol               -> numeric-list
  ## ul               -> itemized-list
  ## dl               -> description-list
  #                        we must wrap dt/dd into an item
  ## dt               -> description
  ## div[figure]      -> figure
  ## table            -> table
  ## thead            -> head
  ## thead th         -> cell
  ## tr               -> row
  ## tr td            -> cell
  ## div[note]        -> note
  ## div[equation]    -> equation
  ## div[example]     -> example
  ## div[section]     -> section[section]
  ## div[chapter]     -> section[chapter]
  ## div[part]        -> section[part]
  ## div[book]        -> book
  ## div[apathy-game] -> apathy-game
  ## div[reference]   -> reference
  ## div[summarize]   -> summarize
  ## title            -> title
  ## p                -> text
  #       only the following may be in a text
  ## span[Apathy]     -> Apathy
  ## span[and]        -> and
  ## span[define]     -> define
  ## span[dollar]     -> dollar
  ## span[footnote]   -> footnote
  ## span[ldquo]      -> ldquo
  ## span[rdquo]      -> rdquo
  ## span[lsquo]      -> lsquo
  ## span[rsquo]      -> rsquo
  ## math             -> math, preserve math
  ## span[mdash]      -> // unicode
  ## span[ndash]      -> // unicode
  ## span[notappl]    -> notappl
  ## span[oslash]     -> // unicode
  ## span[ouml]       -> // unicode
  ## span[percent]    -> percent
  ## span[rightarrow] -> rightarrow
  ## span[trademark]  -> // unicode
  #
  ## REMOVE EVERYTHING ELSE
  #  only "text" may contain TEXT_NODE elements
  pass

def html2category(Node):
  # html
  #  head
  #  body
  #   table
  #    thead
  #     th ... th   ==> these become the default
  #    tbody
  #     tr
  #      td ... td  ==> these become the datum
  #     .
  #     .
  #     tr
  
  ## get the body
  body = None
  for child in Node.childNodes:
    if (child.nodeType == child.ELEMENT_NODE
        and child.tagName.lower() == "body"):
        body = child
        break
  if body is None:
    return None

  ## get the first table
  table = None
  for child in body.childNodes:
    if (child.nodeType == child.ELEMENT_NODE
        and child.tagName.lower() == "table"):
        table = child
        break
  if table is None:
    return None
  else:
    table.tagName = "category"
  
  ## now get the default and it's fields
  # find the thead
  thead = None
  tbody = None
  for child in table.childNodes:
    if child.nodeType == child.ELEMENT_NODE:
      if child.tagName.lower() == "thead":
        thead = child
        thead.tagName = "default"
        for child in thead.childNodes:
          if (child.nodeType == child.ELEMENT_NODE
              and child.tagName.lower() == "th"):
              child.tagName = "field"
              # convert child's interior
      elif child.tagName.lower() == "tbody":
        for tr in child.childNodes:
          if (tr.nodeType == child.ELEMENT_NODE
              and tr.tagName.lower() == "tr"):
              datum = tr.cloneNode(tr)
              datum.tagName = "datum"
              fields = datum.childNodes[:] # bug in minidom
              for field in fields:
                if (field.nodeType == child.ELEMENT_NODE
                    and field.tagName.lower() == "td"):
                    field.tagName = "field"
                    # convert child's interior
                else:
                  datum.removeChild(field)
              table.appendChild(datum)
        table.removeChild(child)
  return table

def convert(Node):
  ## we actually only have one convert function
  Which = Node.tagName
  if Which == "html":
    if Node.hasAttribute("kind"):
      Kind = Node.getAttribute("kind")
      if Kind == "category":
        return html2category(Node)
      else:
        print "Convert to apathy-game"
    else:
      print "Your HTML file is not a recognized apathy-game conversion"
  elif Which == "category":
    print "Category is not supported yet"
  elif Which == "apathy-game":
    print "Apathy-Game is not supported yet"

def apathy2xhtml(fileName):
  xml = xmlParser(fileName)
  for child in xml.childNodes:
    # find the root node
    if child.nodeType == xml.ELEMENT_NODE:
      convert(child)

def xhtml2apathy(fileName):
  xml = xmlParser(fileName)
  for child in xml.childNodes:
    # find the root node
    if child.nodeType == xml.ELEMENT_NODE:
      return convert(child)
  return None