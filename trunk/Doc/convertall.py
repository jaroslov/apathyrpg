import os

xmls = os.listdir(".")

for xml in xmls:
  splitname = os.path.splitext(xml)
  if splitname[1] == ".xml":
    # sanitize math
    #xmlstr = open(xml,"r").read()
    #xmlstr = xmlstr.replace(" xmlns='http://www.w3.org/1998/Math/MathML'","")
    #xmlout = open(xml,"w")
    #print >> xmlout, xmlstr
    command = "xsltproc -o %s.xhtml ../Tools/Apathy2Xhtml.xsl %s.xml"
    command = command%(splitname[0],splitname[0])
    os.system(command)