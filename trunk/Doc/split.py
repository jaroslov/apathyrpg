import os, sys

apathy = open("Apathy.xml","r").read()

parts = apathy.split("hrid=\"")

for part in parts[1:]:
  endquo = part.find('"')
  hrid = part[0:endquo]
  hridp = hrid.split("/")
  hridn = hridp[1]
  if len(hridp) > 2:
    for hr in hridp[2:]:
      hridn += "-"+hr
  print hridn.replace(" ","_").replace(",","_")