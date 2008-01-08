<?php

include_once "config.php";
include_once "xmldb.php";
include_once "ajax.php";

function xod_render_context($node,$attributes,$childNodes,$CoTable) {
  $Id = $node["ID"];
  // build a table
  //  tag-name
  //    attr val    descr
  //    attr val
  //    children...
  $mresult = xod_render($CoTable,$Id,xod_render_context);
  $children = "<ol><li>".implode("</li><li>",$mresult)."</li></ol>";

  $tagName = $node["Name"];
  $nodeValue = $node["Value"];
  // build attributes
  $Attrs = "";
  foreach ($attributes as $attrid => $attribute) {
    $name = $attribute["Name"];
    $value = $attribute["Value"];
    $Attrs .= "<tr><td>$name</td><td>$value</td></tr>";
  }

  $rowspan = sizeof($attributes)+1;

  $table = "";
  $table .= "<table class='xod-table'>";
  $table .= "<thead><th colspan='2'>Aspect</th><th>Description</th></thead>";
  $table .= "<tbody>
              <tr>
                <td class='xod-attr'>Tag Name</td>
                <td>$tagName</td>
                <td rowspan='$rowspan'>Blah</td>
              </tr>
              $Attrs
              <tr><td colspan='3'>$children</td></tr>
            </tbody>";
  $table .= "</table>";
  return $table;
}

function xod_render($CoTable,$Key,$RenderContext=xod_render_context) {
  $result = array();
  $index = 0;
  $number_children = sizeof($CoTable[$Key]);
  foreach ($CoTable[$Key] as $Id => $Child) {
    if ($Child["Kind"] === "element") {
      $index++;
      $ID = $Child["ID"];
      $attributes = xmldb_cot_attributes($CoTable,$ID);
      $childNodes = xmldb_cot_childNodes($CoTable,$ID);
      $result[$ID] = $RenderContext($Child,$attributes,$childNodes,$CoTable);
    }
  }
  return $result;
}

function xod_initialize($replyXML) {
  $Connection = xmldb_create_connection();
  $CoTable = xmldb_child_table_of_document($Connection);

  $cokeys = array_keys($CoTable);
  sort($cokeys);
  $mresult = xod_render($CoTable,$cokeys[0]);
  $result = implode("",$mresult);

  $targets = array("Display");
  $payloads = array($result);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_respond() {
  $reply = $_GET["Message"];
  $replyXML = new DOMDocument();
  $replyXML->loadXML($reply);

  $targets = array();
  $payloads = array();

  foreach ($replyXML->getElementsByTagname("code") as $code) {
    $lres = array("Targets"=>array("Display"),
                  "Payloads"=>array("Unknown Code&#8658;".$code->nodeValue));

    switch ($code->nodeValue) {
    case "Initialize":
      $lres = xod_initialize($replyXML);
      break;
    default: break;
    }

    foreach ($lres["Targets"] as $target)
      array_push($targets,$target);
    foreach ($lres["Payloads"] as $payload)
      array_push($payloads,$payload);
  }
  return arpg_build_responses($targets,$payloads);
}

echo xod_respond();

?>