import codecs
###
#  Convert a Unicode character to a LaTeX code
#
#  The format of the conversion file is this:
#
#  \U... \blah{...}\n
#  ^^^^^ ^^^^^^^^^^
#    1  2    3      4
#
#  1. the unicode character embedded visibly
#  2. a space
#  3. the latex code for that character
#  4. a crlf

# see the various "*.map" files

def buildMapFrom(where):
  Map = {}
  for wh in where:
    print wh
    mappinglines = codecs.open(wh,"r","utf-8").readlines()
    for mappingline in mappinglines:
      umapping = unicode(mappingline)
      key = umapping[0]
      value = umapping[2:].strip()
      Map[key] = value
  return Map

def buildFromCharMap():
  Map = {}
  mappinglines = CharMap.split("\n")
  for mappingline in mappinglines:
    key = mappingline[0]
    value = mappingline[2:].strip()
    Map[key] = value
  return Map

Map = buildMapFrom(["Tools/latin-1.map","Tools/ent-sym-and-greek.map","Tools/basic.map"])

def unicodeToLaTeX(string):
  for key,value in Map.items():
    string = string.replace(key,value)
  return string