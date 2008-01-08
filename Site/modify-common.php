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
    $index++;
    $ID = $Child["ID"];
    if ($Child["Kind"] === "attribute") {
      switch ($Child["Name"]) {
      case "hrid":
        $hrid = $Child["Value"];
        $result[$Child["Name"]] = "<div id='Id$ID'><a ";
        $result[$Child["Name"]] .= "onclick=\""
          .arpg_build_ajax("modify-text.php",
            array("ExpandCategory","Extra"),
            array($ID,$hrid));
        $result[$Child["Name"]] .= "\">Click to expand: $hrid</a></div>";
        break;
      default:
      }
    } else if ($Child["Kind"] === "element") {
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
      case "field":
        $kind = "table";
        $name = $attributes["name"];
        if (array_key_exists("title",$attributes))
          $kind = "title";
        else if (array_key_exists("description",$attributes))
          $kind = "description";
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"field");
        if (!array_key_exists($kind,$result))
          $result[$kind] = array();
        switch ($kind) {
        case "table":
          $result[$kind][$Order] =
            "<div class='field-name'>$name</div>".
            "<div class='field-value'>"
              .implode("",$mresult)
            ."</div>";
          break;
        case "title":
        case "description":
        default:
          $result[$kind][$Order] = implode("",$mresult);
        }
        break;
      case "datum":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"datum");
        $titles_q = $mresult["title"]; // only the first one
        $tables_q = $mresult["table"];
        $descrs_q = $mresult["description"]; // one the first one
        $titles = "<div class='field-name'>Title</div>".
          "<div class='field-value'>".implode("",$titles_q)."</div>";
        $rowspan = sizeof($titles_q) + sizeof($tables_q);
        $descrs = "<div class='field-value' rowspan='$rowspan'>"
          .implode("",$descrs_q)."</div>";
        $tables = "<div class='datum-row'>";
        $tables .= implode("</div><div class='datum-row'>",$tables_q);
        $tables .= "</div>";
        $title = "<div class='datum-row'>$titles</div>";
        $descr = "<div class='datum-row'>$descrs</div>";

        // build table
        $result[$Order] = "<div class='datum'>";
          $result[$Order] .= "<div class='datum-row'>";
            $result[$Order] .= "<div class='field-value'>";
              $result[$Order] .= "<div class='datum' style='width:18em;'>";
                $result[$Order] .= $title;
                $result[$Order] .= $tables;
              $result[$Order] .= "</div>";
            $result[$Order] .= "</div>";
            $result[$Order] .= "<div class='field-value'>";
              $result[$Order] .= "<div class='datum'>";
                $result[$Order] .= $descr;
              $result[$Order] .= "</div>";
            $result[$Order] .= "</div>";
          $result[$Order] .= "</div>";
        $result[$Order] .= "</div>";
        break;
      case "default":
        break;
      case "reference":
        $mresult = arpg_render_text($CoTable,$ID,$Editable,"$Extra/reference");
        $result[$Order] = "<div class='reference'>"
          .implode("",$mresult)
          ."</div>";
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