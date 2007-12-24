<?php

include 'normalize_xml.php';

function populate_apathy($ApathyName,$DatabaseName) {
  $ApathyDom = get_apathy_dom($ApathyName);
  $Connection = create_connection($DatabaseName);
  $HasTextPs = array("title","text","define",
                      "mn","mo","mi",
                      "num","face","bns","bOff",
                      "rOff","raw","kind","mul");
  normalize_xml($DatabaseName,$Connection,
    $ApathyDom,$Connection,$HasTextPs,true);
  //empty_all_xml($Connection);
}

function apathy_is_populated($DatabaseName) {
  $query = "";
}

function test() {
  $ApathyName = "Apathy.xml";
  $ApathyDB = "ApathyRPG";
  populate_apathy($ApathyName,$ApathyDB);
  echo "THERE";
}

test();

?>