<?php

include "arpg.php";
include "ajax.php";

function arpg_load_datum($Response) {
  $Connection = arpg_create_apathy();

  $datum_id = $Response->payload[0];
  $childNodes = xmldb_getChildNodes($Connection,$datum_id);
  $attrs = xmldb_attributesOfSet($Connection,$childNodes);

  $datum_responder = "<a>".print_r($attrs,true)."</a>";

  $targets = array("Datum$datum_id","Fields$datum_id");
  $payloads = array($datum_responder,"FOO");
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_load_category($Response) {
  $Connection = arpg_create_apathy();

  $cat_id = $Response->payload[0];
  $childNodes = xmldb_getChildNodes($Connection,$cat_id);
  $attrs = xmldb_attributesOfSet($Connection,$childNodes);

  $Display = "";
  foreach ($childNodes as $id => $child)
    if ($child["Kind"] === "element"
      and $child["Name"] === "datum") {
        $Display .= "<div class='Datum'>";
        $Display .= "<span id='Datum$id'><a onClick=\"";
        $Display .= arpg_build_ajax("loader.php","LoadDatum",$id);
        $Display .= "\">".$attrs[$id]["name"]["Value"]."</a></span>";
        $Display .= "<div id='Fields$id'></div>";
        $Display .= "</div>";
      }

  $targets = array("Display");
  $payloads = array($Display);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_ajax_collated_categories($Categories) {
  function init($Cats,$CurPath)
    { return "<ol class='CategorySelector'>"; };
  function before($path,$CurPath)
    { return "<li><span>".$path."</span>"; };
  function at_id($path,$id,$CurPath)
    { return "<li><a onClick=\""
        .arpg_build_ajax("loader.php","LoadCategory",$id)
        ."\">".$path."</a></li>"; };
  function after($path,$CurPath)
    { return "</li>"; };
  function finish($Cats,$CurPath)
    { return "</ol>"; };
  $Vis = array("Initialize"=> init,
               "Before"    => before,
               "After"     => after,
               "@ID"       => at_id,
               "Finalize"  => finish);
  return arpg_visit_collated_categories($Categories,$Vis);
}

function arpg_raw_data($Response) {
  $Connection = arpg_create_apathy();
  $categories = arpg_collate_categories($Connection);
  $Selector = arpg_ajax_collated_categories($categories["Content"]);

  $targets = array("Selector");
  $payloads = array($Selector);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_initialize($Response) {
  $Books = "<a onClick=\""
    .arpg_build_ajax("loader.php","LoadBook","")
    ."\">Books</a>";
  $RawData = "<a onClick=\""
    .arpg_build_ajax("loader.php","RawData","")
    ."\">Raw Data</a>";

  $Path = "Apathy Role Playing Game &raquo; $Books | $RawData";

  $targets = array("Path");
  $payloads = array($Path);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_responder() {
  $reply = $_GET["Message"];
  $replyXML = new SimpleXMLElement($reply);
  $targets = array();
  $payloads = array();
  foreach ($replyXML->response as $response) {
    $lres = array("Targets"=>array("Log"),
                  "Payloads"=>array("Unknown Code&rArr;".$response->code[0]));
    switch ($response->code[0]) {
    case "Initialize":
      $lres = arpg_initialize($response);
      break;
    case "RawData":
      $lres = arpg_raw_data($response);
      break;
    case "LoadCategory":
      $lres = arpg_load_category($response);
      break;
    case "LoadDatum":
      $lres = arpg_load_datum($response);
      break;
    }
    foreach ($lres["Targets"] as $target)
      array_push($targets,$target);
    foreach ($lres["Payloads"] as $payload)
      array_push($payloads,$payload);
  }
  return arpg_build_responses($targets,$payloads);
}

echo arpg_responder();

?>