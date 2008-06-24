import os, sys

files = os.listdir(".")

for file in files:
  if os.path.splitext(file)[-1] == ".xhtml":
    cmd = "xmllint --format %s -o %s"%(file, file)
    os.system(cmd)