<?php

include 'config.php';
include 'normalize_xml.php';

function arpg_get_apathy_dom() {
  $name = ARPG_XMLSource;
  $ApathyXml = simplexml_load_file($name);
  $ApathyDom = dom_import_simplexml($ApathyXml);
  return $ApathyDom;
}

function arpg_apathy_serialized_xml_nodes() {
  return array("text");
}

function arpg_internal_arpg_create_apathy() {
  $Connection = xmldb_create_connection();
  if (!xmldb_is_populated($Connection)) {
    $ApathyDom = arpg_get_apathy_dom() or die("No xml");
    $HasTextPs = arpg_apathy_serialized_xml_nodes();
    xmldb_normalize_xml($Connection,$ApathyDom,$Connection,$HasTextPs,true);
  }
  return $Connection;
}

function arpg_create_apathy() {
  return arpg_internal_arpg_create_apathy();
}

function arpg_connect_to_apathy() {
  return xmldb_create_connection();
}

function POPULATE_APATHY_PHP_test() {
  $Connection = arpg_create_apathy();
  if (xmldb_is_populated($Connection))
    echo "Populated";
  else
    die("Unable to open an XML file or a Database.");
}

//POPULATE_APATHY_PHP_test();

?>