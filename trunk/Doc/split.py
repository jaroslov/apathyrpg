import os, sys

rawdata = open("raw-data.xml","r").read()

parts = rawdata.split("</category>")

parts = parts[:-1]

for pdx in xrange(len(parts)):
  parts[pdx] += "</category>"

for part in parts:
  name = part.find("name")
  hrid_b = part.find('"',name)
  hrid_e = part.find('"',hrid_b+1)
  hrid = part[hrid_b+1:hrid_e]
  hridp = hrid.split("/")
  hridn = hridp[1]
  if len(hridp) > 2:
    for hr in hridp[2:]:
      hridn += "-" + hr
  targ = open(hridn.replace(" ","_")+".xml","w")
  print >> targ, part