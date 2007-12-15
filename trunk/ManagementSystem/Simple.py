import sys, os
from Spark import GenericScanner
from Spark import GenericParser

KEYWORDS = [
  "and"
  "bool","break"
  "case","catch","char","concept","concept_map","const","continue",
  "default","do"
  "else","enum",
  "false","float",
  "if", "import", "int",
  "namespace", "not",
  "or",
  "return",
  "struct",
  "xor"]

KEYWORDOPS = ["and","or","not","xor"]

class Token(object):
  def __init__(self,Type,Line,Value="",Offset=0):
    self.Type=Type
    self.Line=Line
    self.Value=Value
    if len(self.Value) == 0:
      self.Value = self.Type
    self.Offset=Offset
  def __cmp__(self,Other):
    if False:
      print >> sys.stderr, "CMP", self, Other, cmp(self.Type,Other)
    if 0 == cmp(self.Type, Other): return 0
    return cmp(self.Value, Other)
  def __repr__(self):
    return str(self)
  def __str__(self):
    return self.toString(indent="")
  def length(self):
    return len(self.Value)
  def toString(self,indent):
    result = indent+'['+self.Type+']'
    if self.Value and len(self.Value)>0:
      result += ' "'+self.Value+'" '
    result += "@(%d,%d)"%(self.Line+1,self.Offset)
    return result

def toString(T, indent):
  if T == None:
    return indent+""
  elif type(T) == type(""):
    return indent+T
  elif type(T) in [tuple, list]:
    res = ""
    if len(T) > 0:
      res += toString(T[0],indent)
    for t in T[1:]:
      res += "\n"+toString(t,indent)
    return res
  return T.toString(indent)

class AST(object):
  def __init__(self,Type,Value=None):
    self.Type = Type
    self.Value = Value
  def __repr__(self):
    return str(self)
  def __str__(self):
    return self.toString(indent="")
  def toString(self,indent):
    res = indent + toString(self.Type,"")
    res += "\n" + toString(self.Value," "+indent)
    return res

class Mapping(AST):
  def __init__(self,Type,Value):
    AST.__init__(self,Type,Value)
  def toString(self,indent):
    res = indent+self.Type+"\n"
    keys = self.Value.keys()
    if len(keys) > 0:
      res += toString(self.Value[keys[0]],indent+" ")
    for key in keys[1:]:
      res += "\n"+toString(self.Value[key],indent+" ")
    return res

class List(AST):
  def __init__(self,Type,Values=None):
    AST.__init__(self,Type)
    self.Value = Values
  def toString(self, indent):
    res = indent+self.Type
    if len(self.Value) > 0:
      res += "\n"+toString(self.Value,indent+" ")
    else:
      res += "\n"+toString("[]",indent+" ")
    return res

class Lexer(GenericScanner):
  def __init__(self,Debug=False):
    GenericScanner.__init__(self)
    self.Line=0
    self.Offset=0
    self.Debug = False
  def tokenize(self, input):
    self.rv = []
    GenericScanner.tokenize(self,input)
    return self.rv
  def t_whitespace(self, s):
    r' \s+ '
    self.Line += s.count('\n')
    nlines = s.split('\n')
    if len(nlines) > 1:
      self.Offset = 0
    self.rv.append(Token("word", self.Line, s, Offset=self.Offset))
    self.Offset += len(nlines[-1])
  def t_tag(self, s):
    r' \<([a-zA-Z]+)\> '
    self.Line += s.count('\n')
    nlines = s.split('\n')
    if len(nlines) > 1:
      self.Offset = 0
    self.rv.append(Token("comment", self.Line, s, Offset=self.Offset))
    self.Offset += len(nlines[-1])

def t_word(self, s):
    r' [a-zA-Z0-9] '
    if self.Debug: print s
    self.rv.append(Token("word", self.Line, s, Offset=self.Offset))
    self.Offset += len(s)
def t_entity(self, s):
    r' \&([a-zA-Z0-9])+\; '
    if self.Debug: print s
    self.rv.append(Token("entity", self.Line, s, Offset=self.Offset))
    self.Offset = len(s)

class Parser(GenericParser):
  def __init__(self, File, Start='file', Debug=False):
    GenericParser.__init__(self, Start)
    self.File = File
    self.Debug = Debug
  def error(self,token):
    print >> sys.stderr, "Syntax error at or near %s"%token
    print >> sys.stderr, self.File[token.Line]
    print >> sys.stderr, " "*(token.Offset)+"^"+"~"*(token.length()-1)
    raise Exception
  def p_file(self, args):
    """
    file ::= toplevel
    """
    return List("File",args[0])

if __name__=="__main__":
  if len(sys.argv) <= 1:
    names = os.listdir("../")
    for ndx in xrange(len(names)):
      names[ndx] = names[ndx]
  else:
    names = sys.argv[1:]
  for name in names:
    if os.path.isfile("../"+name):
      if name.split(".")[-1] != "xml":
        continue
      print >> sys.stderr, name
      test = open("../"+name, "r").read()
      try:
        lexer = Lexer(Debug=True)
        lexer.tokenize(test)
        print >> sys.stderr, lexer
        #parser = Parser(File=test.split('\n'),Debug=False)
        #AST = parser.parse(lexer.rv)
        #print >> sys.stderr, AST
        #typecheck(AST)
      except Exception, e:
        print >> sys.stderr, e
        raise
      except:
        print >> sys.stderr, "Unknown Error"
        #raise