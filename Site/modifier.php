<?php

include "arpg.php";
include "ajax.php";

function arpg_render_text($CoTable,$Key,$Editable=false,$Extra=null) {
  $result = array();
  foreach ($CoTable[$Key] as $Id => $Child) {
    if ($Child["Kind"] === "element") {
      $ID = $Child["ID"];
      $Order = $Child["Order"];
      $attributes = arpg_cot_attributes($CoTable,$ID);
      $childNodes = arpg_cot_childNodes($CoTable,$ID);
      switch ($Child["Name"]) {
      case "text":
        if (!$Editable) {
          $result[$Child["Order"]]
            = arpg_serialize_elements_for_display($Child["Value"]);
          break;
        } else {
          $result[$Child["Order"]] = "<div class='text'><div>"
            .arpg_serialize_elements_for_display($Child["Value"])
            ."</div></div>";
          break;
        }
      case "title":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] = "<div class='title'>"
          .implode("",$mresult)."</div>";
        break;
      case "section":
        $kind = $attributes["kind"];
        $result[$Order] = "<div class='$kind'>";
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] .= implode("",$mresult);
        $result[$Order] .= "</div>";
        break;
      default:
        $result[$Child["Order"]]
          = "<em>Don&#8217;t know: &#8220;".$Child["Name"]."&#8221;</em><br/>";
      }
    }
  }
  return $result;
}

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
                            $childNodes[$keys[0]]["ID"]));
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

function arpg_responder () {
  $reply = $_GET["Message"];
  $replyXML = new SimpleXMLElement($reply);
  $targets = array();
  $payloads = array();

  $Connection = arpg_create_apathy();
  $book = xmldb_getElementsByTagName($Connection,"book");
  $book_ids = array_keys($book);
  $CoTable = arpg_child_table_of_id($Connection,$book_ids[0]);
  $cokeys = array_keys($CoTable);
  sort($cokeys);

  $Selector = arpg_build_selector($CoTable,$cokeys[0]);
  $Display = arpg_build_display($CoTable,$cokeys[0]);

  $targets = array("Selector","Display");
  $payloads = array($Selector,$Display);
  return arpg_build_responses($targets,$payloads);
}

echo arpg_responder();

?>