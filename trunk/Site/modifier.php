<?php

include "arpg.php";
include "ajax.php";

function arpg_render_unknown_text($Child) {
  return "<em>Don&#8217;t know: &#8220;".$Child["Name"]."&#8221;</em><br/>";
}

function arpg_not_description($text) {
  return "Description" !== $text;
}

function arpg_not_title($text) {
  return "Title" !== $text;
}

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
        $result[$Order] = "";
        if (!$Editable) {
          $result[$Order] .= "<a class='section-link' href='#Id$ID'>"
            .arpg_serialize_elements_for_display($Child["Value"])
            ."</a>";
          break;
        } else {
          $result[$Order] .= "<div class='text' id='Id$ID'>"
            ."<div class='inner-text' id='InTxt$ID'>"
            ."<div class='text-homonculus' onclick=\""
            .arpg_build_ajax("text-modify.php","ModifyText",
              "$ID@'+arpg_size('InTxt$ID')+'")
            ."\">Edit</div>"
            .arpg_serialize_elements_for_display($Child["Value"])
            ."</div></div>";
          break;
        }
      case "caption":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] = "<div class='caption'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "cell":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] = "<div class='table-cell-$Extra'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "row":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"row");
        $result[$Order] = "<div class='table-head'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "head":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"head");
        $result[$Order] = "<div class='table-head'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "table":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] = "<div class='tabular'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "figure":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] = "<div class='figure'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "note":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] = "<div class='note'>"
          ."<div class='exclaim'>Note!</div>"
          ."<div class='note-body'>"
          .implode("",$mresult)
          ."</div></div>";
        break;
      case "example":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $mkeys = array_keys($mresult);
        $mkeys = array_filter($mkeys, arpg_not_title);
        $lresult = array();
        foreach ($mkeys as $mkey)
          $lresult[$mkey] = $mresult[$mkey];
        $result[$Order] = "<div class='example'>"
          .$mresult["Title"]
          ."<div class='example-body'>"
          .implode("",$lresult)
          ."</div></div>";
        break;
      case "equation":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result[$Order] = "<div class='equation'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "description":
        $result["Description"] =
          implode("",arpg_render_text($CoTable,$ID,$Editable,$Extra));
        break;
      case "item":
        switch ($Extra) {
        case "numbered-list":
        case "itemized-list":
          $result[$Order] = "<div class='item'>";
          if ($Extra === "itemized-list")
            $result[$Order] .= "<div class='item-indicator'>&#8226;</div>";
          else
            $result[$Order] .= "<div class='item-indicator'>$Order.</div>";
          $result[$Order] .= "<div class='item-body'>";
          $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
          $result[$Order] .= implode("",$mresult);
          $result[$Order] .= "</div></div>";
          break;
        case "description-list":
          $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
          $result[$Order] = "<div class='item'>"
            ."<div class='item-description'>"
            .$mresult["Description"]
            ."</div>"
            ."<div class='item-body'>";
          $mkeys = array_keys($mresult);
          $mkeys = array_filter($mkeys, arpg_not_description);
          $lresult = array();
          foreach ($mkeys as $mkey)
            $lresult[$mkey] = $mresult[$mkey];
          $result[$Order] .= implode("",$lresult);
          $result[$Order] .= "</div></div>";
          break;
        default:
          $result[$Order] = arpg_render_unknown_text($Child);
        }
        break;
      case "description-list":
      case "numbered-list":
      case "itemized-list":
        $list_kind = $Child["Name"];
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$list_kind);
        $result[$Order] = "<div class='$list_kind'>";
        $result[$Order] .= implode("",$mresult);
        $result[$Order] .= "</div>";
        break;
      case "title":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        $result["Title"] = "<div class='title'>"
          .implode("",$mresult)."</div>";
        break;
      case "reference":
        $result[$Order] = "<div class='reference'>"
          ."<a class='referrer'>"
          ."Click to expand table reference: ".$attributes["hrid"]
          ."</a></div>";
        break;
      case "summarize":
        $result[$Order] = "<div class='summarize'>"
          ."<em>Summaries are not rendered in edit-mode.</em>"
          ."</div>";
        break;
      case "section":
        $kind = $attributes["kind"];
        $result[$Order] = "<div class='$kind'>";
        $mresult = arpg_render_text($CoTable,$ID,$Editable,$Extra);
        //$result[$Order] .= implode("",$mresult);
        $result[$Order] .= $mresult["Title"];
        $mkeys = array_keys($mresult);
        $mkeys = array_filter($mkeys, arpg_not_title);
        $lresult = array();
        foreach ($mkeys as $mkey)
          $lresult[$mkey] = $mresult[$mkey];
        $result[$Order] .= implode("",$lresult);
        $result[$Order] .= "</div>";
        break;
      default:
        $result[$Child["Order"]] = arpg_render_unknown_text($Child);
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

function arpg_responder () {
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

  $targets = array("Selector","Display");
  $payloads = array($Selector,$Display);
  return arpg_build_responses($targets,$payloads);
}

echo arpg_responder();

?>