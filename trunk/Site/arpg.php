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

function arpg_visit_collated_categories($Categories,$Visitor,$CurPath) {
  if (!$CurPath) $CurPath = array();
  $result = $Visitor["Initialize"]($Categories,$CurPath);
  foreach ($Categories as $path => $subpath) {
    array_push($CurPath,$path);
    if (array_key_exists("@ID",$subpath))
      $result .= $Visitor["@ID"]($path,$subpath["@ID"],$CurPath);
    else {
      $result .= $Visitor["Before"]($path,$CurPath);
      $result .= arpg_visit_collated_categories($subpath,$Visitor,$CurPath);
      $result .= $Visitor["After"]($path,$CurPath);
    }
  }
  $result .= $Visitor["Finalize"]($Categories);
  return $result;
}

function arpg_pp_collated_visitor() {
  function init($Cats,$CurPath)
    { return "<ol>"; };
  function before($path,$CurPath)
    { return "<li>".$path; };
  function at_id($path,$id,$CurPath)
    { return "<li>".$path." (".$id.")</li>"; };
  function after($path,$CurPath)
    { return "</li>"; };
  function finish($Cats,$CurPath)
    { return "</ol>"; };
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

function arpg_simple_display_map() {
  return array(
          "Apathy"=>"<b>Apathy</b>",
          "and"=>"&amp;",
          "dollar"=>"$",
          "percent"=>"%",
          "rightarrow"=>"&rarr;",
          "ldquo"=>"&ldquo;",
          "rdquo"=>"&rdquo;",
          "lsquo"=>"&lsquo;",
          "rsquo"=>"&rsquo;",
          "mdash"=>"&mdash;",
          "ndash"=>"&ndash;",
          "times"=>"&#215;",
          "ouml"=>"&#246;",
          "oslash"=>"&#248;",
          "trademark"=>"&#8482;",
          "Sum"=>"&#8721;");
}

function arpg_simple_edit_map() {
  return array(
          "Apathy"=>"{@Apathy}",
          "and"=>"&",
          "dollar"=>"$",
          "percent"=>"%",
          "rightarrow"=>"->",
          "ldquo"=>"``",
          "rdquo"=>"''",
          "lsquo"=>"`",
          "rsquo"=>"'",
          "mdash"=>"---",
          "ndash"=>"--",
          "times"=>"{@x}",
          "ouml"=>"{@\\\"o}",
          "oslash"=>"{@/o}",
          "trademark"=>"{@TM}",
          "Sum"=>"{@Sum}");
}

function arpg_invert_map($Map) {
  $imap = array();
  foreach ($Map as $key => $value)
    $imap[$value] = $key;
  return $imap;
}

function argp_simple_translate_for_display($Text,$Strict) {
  $map = arpg_simple_display_map();
  if (array_key_exists($Text,$map))
  return $map[$Text];
    if ($strict)
      return "{@$Text}";
    return $Text;
}

function argp_simple_translate_for_editing($Text,$Strict) {
  $map = arpg_simple_edit_map();
  if (array_key_exists($Text,$map))
  return $map[$Text];
    if ($strict)
      return "{@$Text}";
    return $Text;
}

function argp_serialize_roll($PseudoXML,$STran) {
  $face = "";
  $num = "";
  $bns = "";
  $bOff = "";
  $rOff = "";
  $mul = "";
  $raw = "";
  $kind = "";
  foreach ($PseudoXML->childNodes as $rollparts) {
    $value = $STran($rollparts->nodeValue,false);
    switch ($rollparts->tagName) {
      case "face": $face = $value; break;
      case "num" : $num  = $value; break;
      case "bns" : $bns  = $value; break;
      case "bOff": $bOff = $value; break;
      case "raw" : $raw  = $value; break;
      case "rOff": $rOff = $value; break;
      case "mul" : $mul  = $value; break;
      case "kind": $kind = $value; break;
    }
  }
  return "{@roll $raw$rOff$num"."D$face$bOff$bns$mul$kind}";
}

function argp_serialize_elements_for_Q($PseudoXMLs,$STran) {
  $result = "";
  foreach ($PseudoXMLs as $child)
    if ($child->nodeType == XML_TEXT_NODE)
      $result .= $child->nodeValue;
    else if ($child->nodeType == XML_ELEMENT_NODE)
      switch ($child->tagName) {
        case "root":
          $result .= argp_serialize_elements_for_Q($child->childNodes,$STran);
          break;
        case "roll":
          $result .= argp_serialize_roll($child,$STran);
          break;
        default:
          $result .= $STran($child->tagName,true);
      }
  return $result;
}

function arpg_serialize_elements_for_display($Text) {
  $PseudoXMLstr = "<root>".$Text."</root>";
  $PseudoXML = new DOMDocument();
  $PseudoXML->loadXML($PseudoXMLstr);
  return argp_serialize_elements_for_Q($PseudoXML->childNodes,
    argp_simple_translate_for_display);
}

function arpg_serialize_elements_for_editing($Text) {
  $PseudoXMLstr = "<root>".$Text."</root>";
  $PseudoXML = new DOMDocument();
  $PseudoXML->loadXML($PseudoXMLstr);
  return argp_serialize_elements_for_Q($PseudoXML->childNodes,
    argp_simple_translate_for_editing);
}

function argp_invert_map($map) {
  $result = array();
  foreach ($map as $key => $value)
    $result[$value] = $key;
  return $result;
}

function arpg_deserialize_elements_from_editing($Text) {
  $map = argp_invert_map(arpg_simple_edit_map());
  foreach ($map as $key => $value)
    $Text = str_replace($key,$value,$Text);
  // deserialize roll
  $Text = preg_replace("/\{@roll\s*(([\+\- ])\s*(\d+)\s*[\+])?\s*((\d+)\s*x)?\s*(\d+)\s*[dD]\s*(\d+)\s*(([\+\-])\s*(\d+))?\s*([cCsCpPuUdDfF])?\s*\}/",
    "<roll><rOff>$2</rOff><raw>$3</raw><mul>$5</mul><num>$6</num><face>$7</face><bOff>$9</bOff><bns>$10</bns><kind>$11</kind></roll>",
    $Text);
  // deserialize math
  return $Text;
}

function POPULATE_APATHY_PHP_test() {
  $Connection = arpg_create_apathy();
  if (xmldb_is_populated($Connection))
    echo "Populated<br/>";
  else
    die("Unable to open an XML file or a Database.<br/>");

  $categories = arpg_collate_categories($Connection);
  $path = array("Content");
  echo arpg_ajax_collated_categories($categories);
}

//POPULATE_APATHY_PHP_test();

?>