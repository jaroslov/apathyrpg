<?php

include "arpg.php";
include "ajax.php";

function arpg_unload_datum($Response) {
  $Connection = arpg_create_apathy();
  $datum_id = $Response->payload[0];
  $childNodes = xmldb_getChildNodes($Connection,$datum_id);
  $name = null;
  foreach ($childNodes as $id => $child)
    if ($child["Kind"] === "attribute" and $child["Name"] === "name")
      $name = $child["Value"];
  $datum_responder = "<a onClick=\""
        .arpg_build_ajax("loader.php","LoadDatum",$datum_id)
        ."\">".$name."</a>";

  $targets = array("Datum$datum_id","Fields$datum_id");
  $payloads = array($datum_responder,"<em>Click title to expand.</em>");
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_load_datum($Response) {
  $Connection = arpg_create_apathy();

  $datum_id = $Response->payload[0];
  $childNodes = xmldb_getChildNodes($Connection,$datum_id);
  $allattrs = xmldb_attributesOfSet($Connection,$childNodes);

  $title = null;
  $table = array();
  $other = array();
  $description = null;
  foreach ($allattrs as $id => $attrs)
    if (array_key_exists("title",$attrs)
      and $attrs["title"]["Value"] === "yes")
      $title = $id;
    else if (array_key_exists("table",$attrs)
      and $attrs["table"]["Value"] === "yes")
      $table[$id] = "";
    else if (array_key_exists("description",$attrs)
      and $attrs["description"]["Value"] === "yes")
      $description = $id;
    else
      $other[$id] = "";

  $elementChNodes = array();
  foreach ($childNodes as $id => $child)
    if ($child["Kind"] === "element")
      $elementChNodes[$id] = $child;
  $rawTextNodes = xmldb_getChildNodesOfSet($Connection,$elementChNodes);

  $titleVal = $rawTextNodes[$title];
  foreach ($titleVal as $id => $title)
    $title = $titleVal[$id];
  $descVal = $rawTextNodes[$description];
  foreach ($descVal as $id => $description)
    $description = $descVal[$id];

  $datum_responder = "<a onClick=\""
    .arpg_build_ajax("loader.php","UnloadDatum",$datum_id)
    ."\">".$title["Value"]."</a>";

  $fields_response = "<table class='FieldResponder'>"
                        ."<thead>"
                          ."<th colspan='2'>Aspects</th>"
                          ."<th>Description</th>"
                        ."</thead><tbody>";
  $fields_response .= "<tr><td class='FieldResponderAspect' align='right'>"
                    ."Title</td><td>"
                    .$title['Value']."</td>"
                    ."<td rowspan='"
                    .(sizeof($table)+1)
                    ."'>".$description['Value']."</td></tr>";
  foreach ($table as $id => $S) {
    $tableVal = $rawTextNodes[$id];
    $tkeys = array_keys($tableVal);
    $tableVal = $tableVal[$tkeys[0]];
    $fields_response .= "<tr><td class='FieldResponderAspect' align='right'>"
                      .$allattrs[$id]["name"]["Value"]."</td><td>"
                      .$tableVal["Value"]."</td></tr>";
  }
  $fields_response .= "<tbody></table>";

  $targets = array("Datum$datum_id","Fields$datum_id");
  $payloads = array($datum_responder,$fields_response);
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
        $Display .= "<div id='Fields$id'><em>Click title to expand.</em></div>";
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
    case "UnloadDatum":
      $lres = arpg_unload_datum($response);
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