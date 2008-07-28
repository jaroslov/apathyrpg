import cgi, time
from mod_python import Cookie

def helloworld(req):
  """
  kwyet.cs.tamu.edu/~thechao/index.py/helloworld
  """
  return "<html><body>Hello, World!</body></html>"

def index(req):
  response = "None"
  response = req.form.getfirst('foo','')
  response = cgi.escape(response)

  return "<html><body>%s @ %s</body></html>"%(response, time.time())
