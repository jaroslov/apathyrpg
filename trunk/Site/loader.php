<?php

include "arpg.php";
include "ajax.php";

function arpg_editable_text($Id,$Text) {
  $Text = arpg_serialize_elements_for_display($Text);
  $Text = trim($Text);
  if (strlen($Text) < 1)
    $Text = "<em style='font-variant:small-caps;'>No value</em>";
  $result = "<span onclick=\""
      .arpg_build_ajax("loader.php","ModifyText",
        $Id."@'+this.scrollWidth+':'+this.scrollHeight+'")
      ."\">".$Text."</span>";
  return $result;
}

function arpg_render_raw_text($ChildOfTable,$Ids,
                              $EditText=true,$ExtraInfo="None") {
  $result = array();
  foreach ($Ids as $Id => $Element) {
    $children = array();
    if (array_key_exists($Id,$ChildOfTable))
      $children = $ChildOfTable[$Id];
    if ($Element["Kind"] !== "element")
      continue;
    $Order = $Element["Order"];
    switch ($Element["Name"]) {
    case "text":
      if ($EditText)
        $result[$Order] ="<div class='text' id='Text".$Element["ID"]."'"
                    //." onclick=\""
                    //.arpg_build_ajax("loader.php","ModifyText",
                    //  $Element["ID"]
                    //  ."@'+this.clientWidth+':'+this.clientHeight+'")
                    //."\">"
                    .">"
                    .arpg_editable_text($Element["ID"],$Element["Value"])
                    ."</div>\n";
      else
        $result[$Order] = "<div class='text'>"
                  .arpg_serialize_elements_for_display($Element["Value"])
                  ."</div>";
      break;
    case "note":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,$EditText);
      $result[$Order] =
        "<div class='note'>".implode("\n",$mresult)."</div>";
      break;
    case "item":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,$EditText);
      switch ($ExtraInfo) {
        case "description-list":
          $mkeys = array_keys($mresult);
          $mhead = $mresult[$mkeys[0]];
          $result[$Order] = "<dt>".$mhead."</dt>";
          $mrest = array_splice($mresult,1);
          $result[$Order] .= "<dd>".implode("</dd>\n<dd>",$mrest)."</dd>";
          break;
        default:
          $mresult = arpg_render_raw_text($ChildOfTable,$children,$EditText);
          $result[$Order] = "<li>".implode("</li>\n<li>",$mresult)."</li>";
          break;
      }
      break;
    case "define":
      $result[$Order] = 
        implode("\n",arpg_render_raw_text($ChildOfTable,$children,$EditText));
      break;
    case "description":
      $result[$Order] = 
        "<span class='description'>"
          .implode("\n",
            arpg_render_raw_text($ChildOfTable,$children,$EditText))
        ."</span>";
      break;
    case "description-list":
      $mresult = arpg_render_raw_text($ChildOfTable,
                                      $children,$EditText,"description-list");
      $result[$Order] = 
        "<dl class='description-list'>".implode("\n",$mresult)."</dl>";
      break;
    case "numbered-list":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"numbered-list");
      $result[$Order] = 
        "<ol class='numbered-list'>".implode("\n",$mresult)."</ol>";
      break;
    case "itemized-list":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"itemized-list");
      $result[$Order] = 
        "<ul class='itemized-list'>".implode("\n",$mresult)."</ul>";
      break;
    case "cell":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,$EditText);
      $result[$Order] = "<td>".implode("</td>\n<td>",$mresult)."</td>";
      break;
    case "row":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"table");
      $result[$Order] = "<tr>".implode("\n",$mresult)."</tr>";
      break;
    case "head":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"table");
      $result[$Order] = "<thead>".implode("\n",$mresult)."</thead>";
      break;
    case "table":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"table");
      $result[$Order] = 
        "<table class='figtbl'>".implode("\n",$mresult)."</table>";
      break;
    case "caption":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,$EditText);
      $result[$Order] = "<h2 class='caption'>".implode("\n",$mresult)."</h2>";
      break;
    case "figure":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,$EditText);
      $result[$Order] = "<div class='figure'>".implode("\n",$mresult)."</div>";
      break;
    case "example":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"example");
      $result[$Order]
        = "<div class='example'>".implode("\n",$mresult)."</div>";
      break;
    case "equation":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"example");
      $result[$Order] = 
        "<div class='equation'>".implode("\n",$mresult)."</div>";
      break;
    case "summarize":
    case "reference":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"example");
      foreach ($children as $child)
        if ($child["Kind"] === "attribute"
          and $child["Name"] === "hrid")
          array_push($mresult,$child["Value"]);
      $result[$Order] = 
        "<div class='reference'>".implode("\n",$mresult)."</div>";
      break;
    case "title":
      $mresult = arpg_render_raw_text($ChildOfTable,$children,
                                      $EditText,"Title");
      $result[$Order] = "<h1 class='$ExtraInfo'>"
        .implode("\n",$mresult)."</h1>";
      break;
    case "section":
      // what kind of section?
      $kind = "section";
      foreach ($children as $chid => $child)
        if ($child["Kind"] === "attribute"
          and $child["Name"] === "kind")
          $kind = $child["Value"];
      $mresult = arpg_render_raw_text($ChildOfTable,$children,$EditText,$kind);
      $mkeys = array_keys($mresult);
      $mhead = $mresult[$mkeys[0]];
      $mrest = array_splice($mresult,1);
      $result[$Order] = 
        "<div class='$kind'>".
          $mhead
          ."<div class='section-body'>"
            .implode("\n",$mrest)
          ."</div>"
        ."</div>";
      break;
    case "field":
      $result[$Order] = 
        implode("\n",arpg_render_raw_text($ChildOfTable,$children,$EditText));
        break;
    default:
      $result[$Order] = 
        "{@".$Element["Name"]."===".$Element["Value"]."}";
      break;
    }
  }
  return $result;
}

function arpg_update_text_value($Response) {
  $Connection = arpg_create_apathy();
  $text_id = $Response->payload[0]->who[0];
  $text_value = $Response->payload[0]->what[0];

  $text_value = arpg_deserialize_elements_from_editing($text_value);

  xmldb_setNodeValueById($Connection,$text_id,$text_value);

  $targets = array("Log");
  $payloads = array(time().": ".$text_value);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_unmodify_text($Response) {
  $Connection = arpg_create_apathy();
  $text_id = $Response->payload[0];

  $editable = xmldb_getElementById($Connection,$text_id);

  $targets = array("Text$text_id");
  $payloads = array(arpg_editable_text($text_id,$editable["Value"]));
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_modify_text($Response) {
  $Connection = arpg_create_apathy();
  $at_code = $Response->payload[0];
  $at_codes = explode("@",$at_code);
  $area = $at_codes[1];
  $text_id = $at_codes[0];
  $textNode = xmldb_getElementById($Connection,$text_id);

  $text = $textNode["Value"];

  $wh = explode(":",$area);
  $width = $wh[0];
  $height = $wh[1];
  $emw_guess = 8;
  $emh_guess = 11;
  $width /= $emw_guess;
  $height /= $emh_guess;
  if ($width < 25) $width = 25;
  if ($height < 6) $height = 6;
  $height *= $emh_guess;
  $width *= $emw_guess;


  $editable = "<table class='ModifyTextButton'><tbody><tr>";
  $editable .= "<td colspan='3'>";
  $editable .= "<textarea "
    ."onkeypress=\"markInteresting('UPM$text_id','TA$text_id');\" "
    ."class='SimpleEditor' "
    ."style='height:".$height."px;width:".$width."px;' "
    ."id='TA$text_id'>";
  $editable .= arpg_serialize_elements_for_editing($text);
  $editable .= "</textarea>";
  $editable .= "</td></tr><tr>";
  $editable .= "<td><div onclick=\""
                  .arpg_build_ajax("loader.php","UnmodifyText",$text_id)."\"
                  class='ModifyTextButton'><div>Close</div></div></td>";
  $editable .= "<td>";
  $editable .= "</td>";
  $editable .= "<td align='right'><div id='UPM$text_id'
                  onclick=\"markUninteresting('UPM$text_id','TA$text_id');"
                    .arpg_build_ajax("loader.php","UpdateTextValue",
                      "<who>$text_id</who><what>'+"
                      ."document.getElementById('TA$text_id').value+'"
                      ."</what>"
                    ).";\" "
                  ."class='ModifyTextButton'>"
                  ."<div>Update Database</div></div></td>";
  $editable .= "</tr></tbody></table>";

  $targets = array("Text$text_id");
  $payloads = array($editable);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_unload_datum($Response) {
  $Connection = arpg_create_apathy();
  $datum_id = $Response->payload[0];
  $childNodes = xmldb_getChildNodes($Connection,$datum_id);
  $name = null;
  foreach ($childNodes as $id => $child)
    if ($child["Kind"] === "attribute" and $child["Name"] === "name")
      $name = $child["Value"];
  $datum_responder = "<a onclick=\""
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
  foreach ($titleVal as $id => $S) {
    $titleVal = $titleVal[$id];
    break;
  }

  $ChildOfTable = arpg_child_table_of_id($Connection,$datum_id);

  $title_parts = arpg_render_raw_text($ChildOfTable,$ChildOfTable[$title]);
  $unedtitle_parts = arpg_render_raw_text($ChildOfTable,
                                          $ChildOfTable[$title],false);
  $description_parts = arpg_render_raw_text($ChildOfTable,
                            $ChildOfTable[$description]);

  $datum_responder = "<a onclick=\""
    .arpg_build_ajax("loader.php","UnloadDatum",$datum_id)
    ."\">".implode("<br/>",$unedtitle_parts)."</a>";

  $fields_response = "";
  $fields_response .= "<table><tbody><tr><td valign='top'>";
  $fields_response .= "<table class='FieldResponder'>"
                        ."<thead>"
                          ."<th colspan='2'>Aspects</th>"
                        ."</thead><tbody>";
  $fields_response .= "<tr><td class='FieldResponderAspect' align='right' valign='top'>"
                    ."Title</td><td>"
                    .implode("<br/>",$title_parts)."</td></tr>";
  foreach ($table as $id => $S) {
    $table_parts = arpg_render_raw_text($ChildOfTable,$ChildOfTable[$id]);
    $fields_response .= "<tr><td class='FieldResponderAspect' align='right'>"
                      .$allattrs[$id]["name"]["Value"]."</td><td>"
                      .implode("<br/>",$table_parts)."</td></tr>";
  }
  $fields_response .= "</tbody></table>";
  $fields_response .= "</td><td  valign='top'>";
  $fields_response .= "<table class='FieldResponder'>"
                    . "<thead><th>Description</th></thead><tbody><tr><td>"
                    . implode("<br/>",$description_parts)
                    . "</td></tr></tbody></table>";
  $fields_response .= "</td></tr></tbody></table>";

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
        $Display .= "<span id='Datum$id'><a onclick=\"";
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
    { return "<li><a onclick=\""
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
  return arpg_visit_collated_categories($Categories,$Vis,"");
}

function arpg_load_book($Response) {
  $Connection = arpg_create_apathy();

  $Selector = "&#160;";

  $book = xmldb_getElementsByTagName($Connection,"book");
  $book_ids = array_keys($book);
  $CoTable = arpg_child_table_of_id($Connection,$book_ids[0]);
  $cokeys = array_keys($CoTable);
  sort($cokeys);
  $book_parts = arpg_render_raw_text($CoTable,$CoTable[$cokeys[0]]);
  $Display = implode("",$book_parts);

  $targets = array("Selector","Display");
  $payloads = array($Selector,$Display);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_raw_data($Response) {
  $Connection = arpg_create_apathy();
  $categories = arpg_collate_categories($Connection);
  $Selector = arpg_ajax_collated_categories($categories["Content"]);
  $Display = "&#160;";

  $targets = array("Selector","Display");
  $payloads = array($Selector,$Display);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_initialize($Response) {
  $Books = "<a onclick=\""
    .arpg_build_ajax("loader.php","LoadBook","")
    ."\">Book</a>";
  $RawData = "<a onclick=\""
    .arpg_build_ajax("loader.php","RawData","")
    ."\">Raw Data</a>";

  $Path = "Apathy Role Playing Game &#187; $Books | $RawData";

  $targets = array("Path");
  $payloads = array($Path);

  $lres = arpg_raw_data($Response);
  foreach ($lres["Targets"] as $target)
    array_push($targets,$target);
  foreach ($lres["Payloads"] as $payload)
    array_push($payloads,$payload);
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
    case "LoadBook":
      $lres = arpg_load_book($response);
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
    case "ModifyText":
      $lres = arpg_modify_text($response);
      break;
    case "UnmodifyText":
      $lres = arpg_unmodify_text($response);
      break;
    case "UpdateTextValue":
      $lres = arpg_update_text_value($response);
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