import os, re, sys, optparse, math, time
from Rules import Rules as ApathyRules
from Rules import KeepExcludeRule
from xmple import xmple, MakeFancy, MakePedestrian
from cleandesc import CleanValue, Clean

## Converts a hierarchical folder-based ARPG mgmt sys
#  to a single-folder system. Slower loading, but easier
#  to maintain.
def TraverseRules (Rules, KeyChain=[]):
  for key in Rules.keys():
    if ApathyRules == type(Rules[key]):
      nKeyChain = KeyChain[:]
      nKeyChain.append (key)
      sKey = key.replace(" ","_").replace(",","_")
      print "\t"*(len(KeyChain)-1)+"<"+sKey+">"
      TraverseRules (Rules[key], nKeyChain)
      print "\t"*(len(KeyChain)-1)+"</"+sKey+">"
    else:

      path = ""
      for item in KeyChain:
        path += "/" + item
      print "\t"*(len(KeyChain))+"<KeyedItem",
      location = Rules[key].Location
      location = os.path.splitext(location)[0]
      print "id=\""+location[1:]+"\" >"
      print Rules[key].AsXml (indent="\t"+"\t"*(len(KeyChain)))
      if False:
        val = Rules[key]["description"].Value
        if val:
          if val.find("[LaTeX]") >= 0:
            print >> sys.stderr, val
      print "\t"*(len(KeyChain)-1)+"</KeyedItem>"

def ConvertOldSys ():
  Rules = ApathyRules()
  print "<Content>"
  TraverseRules (Rules)
  print "</Content>"

if __name__=="__main__":
  import xmple2
  arpg_ms = xmple2.ARPG_MS (sys.argv[1])
  print arpg_ms.AsXML ()
