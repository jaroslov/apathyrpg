<?php

include "modify-common.php";

function arpg_build_selector_Q($CoTable,$Key) {
  // builds just an outline
  $result = array();
  $kind = "";
  foreach ($CoTable[$Key] as $Id => $Child) {
    if ($Child["Kind"] === "element") {
      if ($Child["Name"] === "section") {
        $Order = $Child["Order"];
        $ID = $Child["ID"];
        $attrs = arpg_cot_attributes($CoTable,$ID);
        $kind = $attrs["kind"];
        $childNodes = arpg_cot_childNodes($CoTable,$ID);
        $keys = array_keys($childNodes);
        $result[$Order] = implode("",arpg_render_text($CoTable,
                            $childNodes[$keys[0]]["ID"],false,"Link"));
        $mresult = arpg_build_selector_Q($CoTable,$ID);
        $Kind = $mresult["Kind"];
        if (sizeof($mresult["Children"]) > 0)
          $result[$Order] .= "<ol class='$Kind'><li>".implode("</li><li>",
                    $mresult["Children"])."</li></ol>";
      }
    }
  }
  return array("Kind"=>$kind,"Children"=>$result);
}

function arpg_build_selector($CoTable,$Key) {
  $result = arpg_build_selector_Q($CoTable,$Key);
  $Kind = $result["Kind"];
  return "<ol class='$Kind'><li>"
    .implode("</li><li>",$result["Children"])."</li></ol>";
}

function arpg_build_display($CoTable,$Key) {
  return implode("",arpg_render_text($CoTable,$Key,true));
}

function arpg_initial_responder () {
  $reply = $_GET["Message"];
  $replyXML = new SimpleXMLElement($reply);
  $targets = array();
  $payloads = array();

  $Connection = arpg_create_apathy();
  $book = xmldb_getElementsByTagName($Connection,"book");
  $book_ids = array_keys($book);
  $BookCoTable = arpg_child_table_of_id($Connection,$book_ids[0]);
  $bookcokeys = array_keys($BookCoTable);
  sort($bookcokeys);

  $Selector = arpg_build_selector($BookCoTable,$bookcokeys[0]);
  $Display = arpg_build_display($BookCoTable,$bookcokeys[0]);

  $targets = array("Selector-Body","Display");
  $payloads = array($Selector,$Display);
  return arpg_build_responses($targets,$payloads);
}

echo arpg_initial_responder();

?>