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
    r''' \:\= | \: '''
    if self.Debug: print s
    name = "op"
    if s in [";"]:
      name = "statementop"
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
    return List("File",args[0])
  def p_qimport(self, args):
    """
    qimport ::= rimport
    qimport ::= rimport qimport
    """
    if len(args) == 0:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_rimport(self, args):
    """
    rimport ::= import identifier ;
    """
    return AST("Import",args[1])
  def p_toplevel(self, args):
    """
    toplevel ::= rfunction toplevel
    toplevel ::= rnamespace toplevel
    toplevel ::= rstruct toplevel
    toplevel ::= rconcept toplevel
    toplevel ::= rtypedef toplevel
    toplevel ::= 
    """
    if len(args) == 0:
      return []
    else:
      args[1].insert(0, args[0])
      return args[1]
  def p_kindheader(self,args):
    """
    kindheader ::= headernames requiresclause
    """
    m = Mapping("KindHeader",
          {"HeaderNames":List("HeaderNames",args[0]),
           "RequiresClause":args[1]})
    return m
  def p_headernames(self,args):
    """
    headernames ::= headername
    headernames ::= headername , headernames
    """
    if len(args) == 1:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_headername(self,args):
    """
    headername ::= identifier
    headername ::= funop
    """
    return args[0]
  def p_requiresclause(self, args):
    """
    requiresclause ::=
    requiresclause ::= requires < typemeters > { clauses }
    """
    tms = []
    cls = []
    if len(args) > 0:
      tms = args[2]
      cls = args[5]
    return Mapping("RequiresClause",
            {"Typemeters":List("Typemeters",tms),
             "Constraints":List("Clauses",cls)})
  def p_typemeters(self, args):
    """
    typemeters ::= typemeter
    typemeters ::= typemeter , typemeters
    """
    if len(args) == 1:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_typemeter(self, args):
    """
    typemeter ::= identifier : identifier
    typemeter ::= identifier : *
    """
    return Mapping("Typemeter",
            {"Name":AST("Identifier",args[0]),
             "Kind":AST("Kind",args[2])})
  def p_clauses(self, args):
    """
    clauses ::= 
    clauses ::= clause
    clauses ::= clause , clauses
    """
    if len(args) <= 1:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_clause(self, args):
    """
    clause ::= identifier { identifiers }
    """
    return Mapping("Clause",
            {"Constraint":AST("Constraint",args[0]),
             "Parameters":args[2]})
  def p_rtypedef(self, args):
    """
    rtypedef ::= typedef identifiers : type ;
    """
    return Mapping("Typedef",
            {"Identifiers":List("Identifiers",args[1]),
             "Type":AST("Type",args[3])})
  def p_rconcept(self, args):
    """
    rconcept ::= concept kindheader { conceptstatements }
    """
    return Mapping("Concept",
            {"KindHeader":args[1],
             "Block":List("Block",args[3])})
  def p_conceptstatements(self, args):
    """
    conceptstatements ::= 
    conceptstatements ::= rtypedef conceptstatements
    """
    if len(args) == 0:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_rstruct(self, args):
    """
    rstruct ::= struct kindheader { structstatements }
    """
    return Mapping("Struct",
            {"KindHeader":args[1],
             "Block":List("Block",args[3])})
  def p_structstatements(self, args):
    """
    structstatements ::= 
    structstatements ::= declaration structstatements
    """
    if len(args) == 0:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_rnamespace(self, args):
    """
    rnamespace ::= namespace kindheader { toplevel }
    """
    return Mapping("Namespace",
            {"KindHeader":args[1],
             "Block":args[3]})
  def p_rfunction(self, args):
    """
    rfunction ::= barefunction
    rfunction ::= operatorfunction
    """
    return args[0]
  def p_operatorfunction(self, args):
    """
    barefunction ::= operator op requiresclause ( parameters ) : type functionbody
    """
    m = Mapping("Barefunction",
          {"KindHeader":Mapping("KindHeader",
              {"Operator":args[1],
               "Constraints":args[2]}),
           "Parameters":List("Parameters",args[4]),
           "Return":args[7],
           "Body":args[8]})
    return m
  def p_barefunction(self, args):
    """
    barefunction ::= kindheader ( parameters ) : type functionbody
    """
    m = Mapping("Barefunction",
          {"KindHeader":args[0],
           "Parameters":List("Parameters",args[2]),
           "Return":args[5],
           "Body":args[6]})
    return m
  def p_functionbody(self, args):
    """
    functionbody ::= { statements }
    """
    return List("Block",args[1])
  def p_statements(self, args):
    """
    statements ::= statement statements
    statements ::=
    """
    if len(args) == 0:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_statement(self, args):
    """
    statement ::= declaration
    statement ::= declexpr
    statement ::= returnstmt
    statement ::= ifstmt
    """
    return args[0]
  def p_declexpr(self, args):
    """
    declexpr ::= expression ;
    """
    return args[0]
  def p_ifstmt(self, args):
    """
    ifstmt ::= nobranchifstmt
    ifstmt ::= ifelsestmt
    """
    return args[0]
  def p_nobranchifstmt(self, args):
    """
    nobranchifstmt ::= if ( expression ) { statements }
    """
    return Mapping("NoBranchIfStmt",
            {"Expression":args[2],
             "Block":args[5]})
  def p_ifelsestmt(self, args):
    """
    ifelsestmt ::= if ( expression ) { statements } else { statements }
    """
    return Mapping("IfElseStmt",
            {"Expression":AST("Expression",args[2]),
             "TrueBlock":AST("TrueBlock",args[5]),
             "FalseBlock":AST("FalseBlock",args[9])})
  def p_returnstmt(self, args):
    """
    returnstmt ::= return expression ;
    """
    return List("Return",[args[1]])
  def p_declaration(self, args):
    """
    declaration ::= simpledecl ;
    declaration ::= defaultdecl ;
    """
    return args[0]
  def p_simpledecl(self, args):
    """
    simpledecl ::= identifiers : type
    """
    return "SimplDecl"
  def p_defaultdecl(self, args):
    """
    defaultdecl ::= identifiers : type := expression
    """
    m = Mapping("DefaultDecl",
          {"Identifiers":args[0],
           "Type":args[2],
           "Expression":args[4]})
    return m
  def p_expression(self, args):
    """
    expression ::= trivialexpr
    expression ::= binaryexpr
    expression ::= postexpr
    expression ::= preexpr
    expression ::= parentheticalexpr
    """
    return [args[0]]
  def p_preexpr(self, args):
    """
    expression ::= preop expression
    """
    return Mapping("PreExpr",
            {"Expression":args[1],
             "Postoperation":args[0]})
  def p_preop(self, args):
    """
    preop ::= ++
    preop ::= --
    preop ::= !
    preop ::= ~
    preop ::= *
    preop ::= -
    preop ::= +
    preop ::= &
    """
    return AST("Operation",args[0])
  def p_postexpr(self, args):
    """
    expression ::= expression postop
    """
    return Mapping("PostExpr",
            {"Expression":args[0],
             "Postoperation":args[1]})
  def p_postop(self, args):
    """
    postop ::= ( arguments )
    postop ::= ++
    postop ::= --
    """
    if len(args) == 1:
      return AST("Operation",args[0])
    else:
      return AST("FunctionCall",args[1])
  def p_funparameters(self, args):
    """
    arguments ::= 
    arguments ::= identifiers
    """
    return List("Arguments",args)
  def p_binaryexpr(self, args):
    """
    binaryexpr ::= expression op expression
    """
    return Mapping("Binary",
            {"Left":args[0],
             "Operator":AST("Operator",args[1]),
             "Right":args[2]})
  def p_parentheticalexpr(self, args):
    """
    parentheticalexpr ::= ( expression )
    """
    return args[1]
  def p_trivialexpr(self, args):
    """
    trivialexpr ::= identifier
    trivialexpr ::= number
    """
    return AST("Expression",args[0])
  def p_identifiers(self, args):
    """
    identifiers ::= identifier
    identifiers ::= identifier , identifiers
    """
    if len(args) == 1:
      return [AST("Identifier",args[0])]
    else:
      args[-1].insert(0, AST("Identifier",args[0]))
      return args[-1]
  def p_parameters(self, args):
    """
    parameters ::= 
    parameters ::= parameter
    parameters ::= parameter , parameters
    """
    if len(args) <= 1:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_parameter(self, args):
    """
    parameter ::= identifier : type
    parameter ::= identifier : type := expression
    """
    m = Mapping("Parameter",
          {"Identifier":AST("Identifier",args[0]),
           "Type":args[2]})
    if len(args) == 5:
      m.Value["Expression"] = args[4]
    return m
  def p_type(self, args):
    """
    type ::= simpletype
    type ::= modifiedtype
    """
    return args[0]
  def p_simpletype(self, args):
    """
    simpletype ::= identifier
    simpletype ::= instantiatedtype
    simpletype ::= builtintype
    """
    return args[0]
  def p_modifiedtype(self, args):
    """
    modifiedtype ::= modifiers simpletype
    """
    return Mapping("ModifiedType",
            {"Modifiers":List("Modifiers",args[0]),
             "Type":AST("Type",args[1])})
  def p_modifiers(self, args):
    """
    modifiers ::= modifier modifiers
    modifiers ::= modifier
    """
    if len(args)==1:
      return args
    else:
      args[-1].insert(0, args[0])
      return args[-1]
  def p_modifier(self, args):
    """
    modifier ::= *
    modifier ::= const
    modifier ::= mutable
    modifier ::= [ ]
    """
    if len(args) == 1:
      return AST("Modifier",args[0])
    elif len(args) == 2:
      if args[0] == "[":
        return AST("Modifier","array")
  def p_instantiatedtype(self, args):
    """
    instantiatedtype ::= identifier { identifiers }
    """
    return Mapping("InstantiatedType",
            {"Base":AST("Base",args[0]),
             "Instances":List("Instances",args[2])})
  def p_builtintype(self, args):
    """
    builtintype ::= literalbuiltintype
    builtintype ::= instancebuiltintype
    """
    return args[0]
  def p_literalbuiltintype(self, args):
    """
    literalbuiltintype ::= bool
    literalbuiltintype ::= char
    literalbuiltintype ::= int
    literalbuiltintype ::= float
    """
    return AST("LitBuiltIn",args[0])
  def p_instancebuiltintype(self, args):
    """
    instancebuiltintype ::= literalbuiltintype { expression }
    """
    return Mapping("InstLitBuiltinType",
            {"Type":args[0],
             "Instance":List("Instance",args[2])})

typecheck(AST):
  pass

if __name__=="__main__":
  if len(sys.argv) <= 1:
    names = os.listdir(".")
    for ndx in xrange(len(names)):
      names[ndx] = names[ndx]
  else:
    names = sys.argv[1:]
  for name in names:
    if os.path.isfile(name):
      if name.split(".")[-1] != "lem":
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