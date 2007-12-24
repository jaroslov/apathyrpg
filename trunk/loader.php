<?php

include "ajax.php";
include "populate_apathy.php";

function initialize_system($target,$source,$code,$message,$apathy) {
  $payloads = array();
  array_push($payloads,make_main_menu("Choose"));
  array_push($payloads,"<em>No data shown.</em>");
  $targets = array();
  array_push($targets,"Path");
  array_push($targets,"Datum");
  return build_responses($targets,$payloads);
}

function respond($environment) {
  $env = get_environment();
  $con = connect_to_apathy("Apathy.xml");
  /*$apathy = $apathydom->ownerDocument;
  if ("Initialize" === $code) {
    return initialize_system($trg,$src,$code,$msg,$apathy);
  } else if ("NoResponse" === $code) {
    return build_empty_response();
  } else if ("RawData" === $code) {
    return raw_data_response($trg,$src,$code,$msg,$apathy);
  } else if ("LoadCategory" === $code) {
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