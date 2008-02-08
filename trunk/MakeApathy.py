#!/usr/bin/env python2.5

import os

buildWebpage = "Tools/Builder.py --prefix=Doc/ -wc --retarget-resources"
buildLaTeX = "Tools/Builder.py --prefix=Doc/ -l --retarget-resources"

os.system(buildWebpage)
os.system(buildLaTeX)
