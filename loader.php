<?php

include "ajax.php";
include "populate_apathy.php";

function make_main_menu($which) {
  $options = array();
  $chsel = false;
  $bksel = false;
  $rdsel = false;
  switch ($which) {
    case "Choose": $chsel = true; break;
    case "Book": $bksel = true; break;
    case "RawData": $rdsel = true; break;
  }
  array_push($options,make_option_for_select("RebuildMainMenu","Choose...",$chsel));
  array_push($options,make_option_for_select("Book","Book",$bksel));
  array_push($options,make_option_for_select("RawData","Raw Data",$rdsel));
  $env = array("Responder"=>"loader.php",
               "Target"=>"'Path'",
               "Source"=>"'Path'",
               "Code"=>"value",
               "Message"=>"''");
  return make_select_statement($options,$env);
}

function make_arrow_path($parts) {
  $raquo = "&raquo;";
  $path = "<span class='CurrentPosition'>";
  if (is_array($parts))
    foreach ($parts as $part)
      $path .= $raquo . " " . $part . " ";
  else
    $path .= $parts;
  return $path."</span>";
}

function load_category_path($environment) {
  $catparts = array();
  $catnames
    = xmldb_getAttributeOfTagName($environment["Connection"],"category","name");
  $options = array();
  foreach ($catnames as $catname) {
    $catpath = $catname["Value"];
    $catpathparts = explode("/",$catpath);
    array_push($options,make_option_for_select("NoResponse",$catpath,false));
  }
  $select = make_select_statement($options,$env);
  array_push($catparts,$select);
  return $catparts;
}

function raw_data_response($environment) {
  // we're going to build a path
  // [main-menu] >> Raw Data >> [Content/]
  $parts = array();
  $parts[0] = "Raw Data";

  $environment["Message"] = "Content/";
  $catpath = load_category_path($environment);
  foreach ($catpath as $catpart)
    array_push($parts,$catpart);

  $result = make_main_menu("RawData") . " " . make_arrow_path($parts);
  return build_response("Path",$result);
}

function initialize_system($environment) {
  $env["Connection"] = create_apathy("Apathy.xml");
  $payloads = array();
  array_push($payloads,make_main_menu("Choose"));
  array_push($payloads,"<em>No data shown.</em>");
  $targets = array();
  array_push($targets,"Path");
  array_push($targets,"Datum");
  return build_responses($targets,$payloads);
}

function respond() {
  $env = get_environment();
  $env["Connection"] = connect_to_apathy();
  if ("Initialize" === $env["Code"]) {
    return initialize_system($env);
  } else if ("NoResponse" === $env["Code"]) {
    return build_empty_response();
  } else if ("RebuildMainMenu" === $env["Code"]) {
    return build_response("Path",make_main_menu("Choose"));
  } else if ("RawData" === $env["Code"]) {
    return raw_data_response($env);
  } /*else if ("LoadCategory" === $code) {
    return load_category_response($trg,$src,$code,$msg,$apathy);
  } else if ("LoadDatum" === $code) {
    return load_datum_response($trg,$src,$code,$msg,$apathy);
  } else if ("LogMessage" === $code) {
    return build_response("Log",
      "<b>\"</b><span style='color:green;'>".$msg."</span><b>\"</b>");
  } else {
    if (false !== strpos($code,"@")) {
      return update_value_response($trg,$src,$code,$msg,$apathydom);
    }
  }*/
  return build_response("Log",
      "<p style='color:red;' >Not a known code:".$env["Code"]
      ." with ".$env["Target"]."->".$env["Source"]
      ."@".$env["Message"]." with ("
      .$con.")</p>");
}

echo respond();

?>