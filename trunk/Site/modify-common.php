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

function arpg_render_inner_text($Id,$Text,$Extra) {
  return  "<div class='inner-text' id='InTxt$Id' onclick=\""
            .arpg_build_ajax("modify-text.php",
              array("ModifyText","Extra"),array($Id,$Extra))
            ."\">"
            .arpg_serialize_elements_for_display($Text)
            ."</div>";
}

function arpg_render_text($CoTable,$Key,$Editable=false,$Extra=null) {
  $result = array();
  $index = 0;
  $number_children = sizeof($CoTable[$Key]);
  foreach ($CoTable[$Key] as $Id => $Child) {
    if ($Child["Kind"] === "element") {
      $index++;
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
            .arpg_render_inner_text($ID,$Child["Value"],"$Extra/text")
            ."</div>";
          break;
        }
      case "caption":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"caption");
        $result[$Order] = "<div class='caption'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "cell":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"$Extra/cell");
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
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"table");
        $result[$Order] = "<div class='tabular'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "figure":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"figure");
        $result[$Order] = "<div class='figure'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "note":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"note");
        $result[$Order] = "<div class='note'>"
          ."<div class='exclaim'>Note!</div>"
          ."<div class='note-body'>"
          .implode("",$mresult)
          ."</div></div>";
        break;
      case "example":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"example");
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
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"equation");
        $result[$Order] = "<div class='equation'>"
          .implode("",$mresult)
          ."</div>";
        break;
      case "description":
        $result["Description"] =
          implode("",arpg_render_text($CoTable,$ID,$Editable,"$Extra/description"));
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
          $mresult = arpg_render_text($CoTable,$ID,$Editable,"$Extra/item");
          $result[$Order] .= implode("",$mresult);
          $result[$Order] .= "</div></div>";
          break;
        case "description-list":
          $mresult = arpg_render_text($CoTable,$ID,$Editable,"$Extra/item");
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
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"$Extra/title");
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
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"$kind");
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

?>