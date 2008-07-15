<?php

$svnup = system("svn update");

$showwebpage = system("./Tools/build_doc.py --prefix=Doc/ -w --retarget-resources")

?>
