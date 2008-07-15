<?php

$svnup = system("svn update");

$showwebpage = system("./Tools/build_doc.py --prefix=Doc/ -l --retarget-resources > tmp.tex")

?>
