from lxml import etree
import cgi, time, os, sys
from mod_python import Cookie, apache, util

primary_path = "/home/thechao/public_html/Apathy"

def basic_html_page(Title="", Body=None, Href="../Doc/Apathy/apathy.css"):#/home/thechao/public_html/Apathy/Doc/Apathy/apathy.css"):
  html = etree.Element("html")
  head = etree.SubElement(html, "head")
  title = etree.SubElement(head, "title")
  title.text = Title
  link = etree.SubElement(head, "link", rel="stylesheet", type="text/css", href="%s"%Href)
  body = etree.SubElement(html, "body")
  if Body is not None:
    body.append(Body)
  return html

def list_available_docs(initial_path):
  docs_path = os.path.join(primary_path, initial_path)
  AvailableDocs = os.listdir(docs_path)
  AvailableDocs.sort()
  ol = etree.Element("ol")
  for doc in AvailableDocs:
    doc_path = os.path.join(docs_path, doc)
    if os.path.isdir(doc_path) and doc[0] != '.':
      li = etree.SubElement(ol, "li")
      a = etree.SubElement(li, "a")
      a.text = doc
      a.set('href', 'index.py?gotodoc=%s'%os.path.join(initial_path, doc))
    if os.path.isfile(doc_path) and os.path.splitext(doc_path)[-1] == ".xhtml":
      try:
        table_p = etree.parse(doc_path).getroot()
      except:
        continue
      if table_p.tag == "table":
        li = etree.SubElement(ol, "li")
        em = etree.SubElement(li, "em")
        em.text = "Edit Table: "
        a = etree.SubElement(li, "a")
        a.text = table_p.get('name')
        a.set('href','index.py?edittable=%s'%os.path.join(initial_path, doc))
  return ol

def gotodoc(request, cur_doc_path):
  ol = list_available_docs(cur_doc_path)
  return etree.tostring(basic_html_page(Body=ol), pretty_print=True)

def edittable(request, cur_doc_path):
  doc_path = os.path.join(primary_path, cur_doc_path)
  table = etree.parse(doc_path).getroot()
  return etree.tostring(basic_html_page(Body=table), pretty_print=True)

def index(req):
  parameters = util.FieldStorage(req)
  if 'gotodoc' in parameters.keys():
    return gotodoc(req, parameters['gotodoc'])
  elif 'edittable' in parameters.keys():
    return edittable(req, parameters['edittable'])

  ol = list_available_docs("Doc")

  return etree.tostring(basic_html_page(Title="ApathyRPG Table Editor",Body=ol), pretty_print=True)
