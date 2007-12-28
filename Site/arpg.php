<?php

include 'config.php';
include 'xmldb.php';

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

function arpg_get_categories($Connection) {
  $childNodes = xmldb_getElementsByTagName($Connection,"category");
  return xmldb_attributesOfSet($Connection,$childNodes);
}

function arpg_visit_collated_categories($Categories,$Visitor) {
  $result = $Visitor["Initialize"]($Categories);
  foreach ($Categories as $path => $subpath) {
    if (array_key_exists("@ID",$subpath))
      $result .= $Visitor["@ID"]($path,$subpath["@ID"]);
    else {
      $result .= $Visitor["Before"]($path);
      $result .= arpg_visit_collated_categories($subpath,$Visitor);
      $result .= $Visitor["After"]($path);
    }
  }
  $result .= $Visitor["Finalize"]($Categories);
  return $result;
}

function arpg_pp_collated_visitor() {
  function init($Cats) { return "<ol>"; };
  function before($path) { return "<li>".$path; };
  function at_id($path,$id) { return "<li>".$path." (".$id.")</li>"; };
  function after($path) { return "</li>"; };
  function finish($Cats) { return "</ol>"; };
  $Vis = array("Initialize"=> init,
               "Before"    => before,
               "After"     => after,
               "@ID"       => at_id,
               "Finalize"  => finish);
  return $Vis;
}

function arpg_pp_collated_categories($Categories) {
  return arpg_visit_collated_categories($Categories,
    arpg_pp_collated_visitor());
}

/*function arpg_pp_collated_categories($Categories) {
  $result = "<ol>";
  foreach ($Categories as $path => $subpath) {
    $result .= "<li>" . $path;
    if (array_key_exists("@ID",$subpath))
      $result .= " (".$subpath["@ID"].")";
    else
      $result .= arpg_pp_collated_categories($subpath);
    $result .= "</li>";
  }
  return $result."</ol>";
}*/

function arpg_retrieve_by_path($Categories,$path) {
  $local = &$Categories;
  foreach ($path as $part)
    if (array_key_exists($part,$local))
      $local = &$local[$part];
  return $local;
}

function arpg_collate_categories_Q($Categories) {
  // an id-keyed list of string
  $collation = array();
  foreach ($Categories as $id => $attributes) {
    $path = explode("/",$attributes["name"]["Value"]);
    $local = &$collation;
    foreach ($path as $part) {
      if (!array_key_exists($part,$local))
        $local[$part] = array();
      $local = &$local[$part];
    }
    $local["@ID"] = $id;
  }
  return $collation;
}

function arpg_collate_categories($Connection) {
  return arpg_collate_categories_Q(arpg_get_categories($Connection));
}

function POPULATE_APATHY_PHP_test() {
  $Connection = arpg_create_apathy();
  if (xmldb_is_populated($Connection))
    echo "Populated<br/>";
  else
    die("Unable to open an XML file or a Database.<br/>");

  $categories = arpg_collate_categories($Connection);
  $path = array("Content");
  echo arpg_pp_collated_categories(arpg_retrieve_by_path($categories,$path));
}

POPULATE_APATHY_PHP_test();

?>