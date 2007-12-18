import sys, os
from Spark import GenericScanner
from Spark import GenericParser

KEYWORDS = []

KEYWORDOPS = []

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
    self.Offset += len(nlines[-1])
  def t_identifier(self, s):
    r' ([a-zA-Z_][a-zA-Z_0-9]*)|(`(\\`|[^`])*`) '
    if self.Debug: print s
    if s in KEYWORDOPS:
      self.rv.append(Token("op",self.Line,s,Offset=self.Offset))
    elif s in KEYWORDS:
      self.rv.append(Token(s, self.Line, Offset=self.Offset))
    else:
      self.rv.append(Token("identifier", self.Line, s, Offset=self.Offset))
    self.Offset += len(s)
  def t_string(self,s):
    r' "(\\"|[^"])*" '
    if self.Debug: print s
    self.rv.append(Token("string",self.Line,s.strip('"'),Offset=self.Offset))
    self.Offset += len(s)
  def t_character(self,s):
    r" '(\\'|[^'])*' "
    if self.Debug: print s
    self.rv.append(Token("character",self.Line,s.strip("'"),Offset=self.Offset))
    self.Offset += len(s)
  def t_scope(self,s):
    r' [\{\}] '
    if self.Debug: print s
    self.rv.append(Token(s,self.Line,Offset=self.Offset))
    self.Offset += len(s)
  def t_comment(self,s):
    r' (\#([^\n]*)(\n)?) '
    self.Offset += 0
    self.Line += 1
    if len(self.rv)>0:
      self.rv[-1].Comment = (Token("comment",self.Line,s[:-1],Offset=self.Offset))
  def t_operator(self,s):
    r''' \:\= | \: | \[\] | , '''
    if self.Debug: print s
    name = "op"
    self.rv.append(Token(name,self.Line,s,Offset=self.Offset))
    self.Offset += len(s)

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
    return args
  def p_toplevel(self, args):
    """
    toplevel ::= topitem
    toplevel ::= topitem toplevel
    """
    if len(args) > 0:
      args[-1].insert(0, args[0])
      return args[-1]
    return args
  def p_topitem(self, args):
    """
    topitem ::= decltypedef
    topitem ::= declstruct
    topitem ::= 
    """
    return args[0]

if __name__=="__main__":
  if len(sys.argv) <= 1:
    names = os.listdir(".")
    for ndx in xrange(len(names)):
      names[ndx] = names[ndx]
  else:
    names = sys.argv[1:]
  for name in names:
    if os.path.isfile(name):
      if name.split(".")[-1] != "flc":
        continue
      print >> sys.stderr, name
      test = open(name, "r").read()
      try:
        lexer = Lexer(Debug=True)
        lexer.tokenize(test)
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