<?php

include 'normalize_xml.php';

function arpg_get_apathy_dom($name) {
  if ($name === null)
    $name = "Apathy.xml";
  $ApathyXml = simplexml_load_file($name);
  $ApathyDom = dom_import_simplexml($ApathyXml);
  return $ApathyDom;
}

function arpg_apathy_serialized_xml_nodes() {
  return array("text");
}

function arpg_FORCE_arpg_create_apathy($Connection,$DatabaseName,$ApathyDom) {
}

function arpg_internal_arpg_create_apathy($ApathyName,$DatabaseName) {
  $Connection = xmldb_create_connection($DatabaseName);
  if (!xmldb_is_populated($Connection)) {
    $ApathyDom = arpg_get_apathy_dom($ApathyName) or die("No xml");
    $HasTextPs = arpg_apathy_serialized_xml_nodes();
    xmldb_normalize_xml($DatabaseName,$Connection,
                  $ApathyDom,$Connection,$HasTextPs,true);
  }
  return $Connection;
}

function arpg_create_apathy($ApathyName) {
  return arpg_internal_arpg_create_apathy($ApathyName,"ApathyRPG");
}

function arpg_connect_to_apathy() {
  return xmldb_create_connection("ApathyRPG");
}

function POPULATE_APATHY_PHP_test() {
  $ApathyName = "Apathy.xml";
  $ApathyDB = "ApathyRPG";
  $Connection = arpg_create_apathy($ApathyName,$ApathyDB);
  if (xmldb_is_populated($Connection))
    echo "Populated";
  else
    die("Unable to open an XML file or a Database.");
}

//POPULATE_APATHY_PHP_test();

?>