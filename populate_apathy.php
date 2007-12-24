<?php

include 'normalize_xml.php';

function FORCE_create_apathy($Connection,$DatabaseName,$ApathyDom) {
  $HasTextPs = array("title","text","define",
                      "mn","mo","mi",
                      "num","face","bns","bOff",
                      "rOff","raw","kind","mul");
  normalize_xml($DatabaseName,$Connection,
    $ApathyDom,$Connection,$HasTextPs,true);
  //empty_all_xml($Connection);
}

function create_apathy($ApathyName,$DatabaseName) {
  $Connection = create_connection($DatabaseName);
  if (!xmldb_is_populated($Connection)) {
    $ApathyDom = get_apathy_dom($ApathyName);
    FORCE_create_apathy($Connection,$DatabaseName,$ApathyDom);
  }
  return $Connection;
}

function test() {
  $ApathyName = "Apathy.xml";
  $ApathyDB = "ApathyRPG";
  $Connection = create_apathy($ApathyName,$ApathyDB);
  if (xmldb_is_populated($Connection))
    echo "Populated";
  else
    die("Unable to open an XML file or a Database.");
  $sections = xmldb_getElementsByTagName($Connection,"section");
  foreach ($sections as $section) {
    $attrs = xmldb_attributes($Connection,$section);
    echo "<br/>";
    print_r($section);
    foreach ($attrs as $attr)
      echo "&nbsp;&nbsp;&nbsp;<em>".$attr["Name"]." = ".$attr["Value"]."</em><br/>";
  }
}

test();

?>