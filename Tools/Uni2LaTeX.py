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

def buildMapFrom(where):
  Map = {}
  for wh in where:
    mappinglines = codecs.open(wh,"r","utf-8").readlines()
    for mappingline in mappinglines:
      umapping = unicode(mappingline)
      key = umapping[0]
      value = umapping[2:].strip()
      Map[key] = value

def unicodeToLaTeX(string):
  for key,value in Map.items():
    string = string.replace(key,value)
  return string

lines = codecs.open("ent-sym-and-greek.txt","r","utf-8").readlines()
res = codecs.open("ent-sym-and-greek.map","w","utf-8")
for line in lines:
  parts = line.split(" ")
  print >> res, parts[-1].strip()