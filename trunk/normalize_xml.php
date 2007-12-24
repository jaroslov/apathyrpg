<?php

// Normalize XML
// convert an XML document into a database
include 'apathy_xml.php';

function create_connection($Database) {
  // Connecting, selecting database
  $link = mysql_connect('localhost', 'thechao', 'ha1l3r1S')
      or die('Could not connect: ' . mysql_error());  
  mysql_select_db($Database) or die('Could not select database');
  return $link;
}

function normalize_xml($Document,$Connection,$HasTextPs) {
  $Node = $Document->ownerDocument;
}

function test() {
  $ApathyName = "Apathy.xml";
  $ApathyDom = get_apathy_dom($ApathyName);
  $Connection = create_connection("ApathyRPG");
  $HasTextPs = array("title","text","define",
                      "mn","mo","mi",
                      "num","face","bns","bOff",
                      "rOff","raw","kind","mul");
  normalize_xml($ApathyDom,$Connection,$HasTextPs);
}

test();

?>