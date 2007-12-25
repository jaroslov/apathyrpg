<?php

include 'normalize_xml.php';

function apathy_serialized_xml_nodes() {
  return array("title","text","define","field",
              "mn","mo","mi",
              "num","face","bns","bOff",
              "rOff","raw","kind","mul");
}

function FORCE_create_apathy($Connection,$DatabaseName,$ApathyDom) {
}

function internal_create_apathy($ApathyName,$DatabaseName) {
  $Connection = create_connection($DatabaseName);
  if (!xmldb_is_populated($Connection)) {
    $ApathyDom = get_apathy_dom($ApathyName);
    $HasTextPs = apathy_serialized_xml_nodes();
    normalize_xml($DatabaseName,$Connection,
                  $ApathyDom,$Connection,$HasTextPs,true);
  }
  return $Connection;
}

function create_apathy($ApathyName) {
  return internal_create_apathy($ApathyName,"ApathyRPG");
}

function connect_to_apathy() {
  return create_connection("ApathyRPG");
}

function POPULATE_APATHY_PHP_test() {
  $ApathyName = "Apathy.xml";
  $ApathyDB = "ApathyRPG";
  $Connection = create_apathy($ApathyName,$ApathyDB);
  if (xmldb_is_populated($Connection))
    echo "Populated";
  else
    die("Unable to open an XML file or a Database.");
}

//POPULATE_APATHY_PHP_test();

?>