<?php

include "arpg.php";
include "ajax.php";

function arpg_simple_display_map() {
  return array(
          "<Apathy/>"=>"<b>Apathy</b>",
          "<and/>"=>"&amp;",
          "<dollar/>"=>"$",
          "<percent/>"=>"%",
          "<rightarrow/>"=>"&rarr;",
          "<ldquo/>"=>"&ldquo;",
          "<rdquo/>"=>"&rdquo;",
          "<lsquo/>"=>"&lsquo;",
          "<rsquo/>"=>"&rsquo;",
          "<mdash/>"=>"&mdash;",
          "<ndash/>"=>"&ndash;",
          "<times/>"=>"&#215;",
          "<ouml/>"=>"&#246;",
          "<oslash/>"=>"&#248;",
          "<trademark/>"=>"&#8482;",
          "<Sum/>"=>"&#8721;");
}

function arpg_simple_edit_map() {
  return array(
          "<Apathy/>"=>"{Apathy}",
          "<and/>"=>"&",
          "<dollar/>"=>"$",
          "<percent/>"=>"%",
          "<rightarrow/>"=>"->",
          "<ldquo/>"=>"``",
          "<rdquo/>"=>"''",
          "<lsquo/>"=>"`",
          "<rsquo/>"=>"'",
          "<mdash/>"=>"---",
          "<ndash/>"=>"--",
          "<times/>"=>"{x}",
          "<ouml/>"=>"{\\\"o}",
          "<oslash/>"=>"{/o}",
          "<trademark/>"=>"{TM}",
          "<Sum/>"=>"{Sum}");
}

function arpg_inverse_map($Map) {
  $imap = array();
  foreach ($Map as $key => $value)
    $imap[$value] = $key;
  return $imap;
}

function argp_simple_translate_for_display($Text,$Strict) {
  switch ($Text) {
    case "percent": return "%"; break;
    case "ldquo": return "&ldquo;"; break;
    case "rdquo": return "&rdquo;"; break;
    case "lsquo": return "&lsquo;"; break;
    case "rsquo": return "&rsquo;"; break;
    case "mdash": return "&mdash;"; break;
    case "ndash": return "&ndash;"; break;
    case "dollar": return "$"; break;
    case "and": return "&amp;"; break;
    case "notappl": return "<em>n/a</em>"; break;
    default:
      if ($strict)
        return "{@$Text}";
      return $Text;
  }
}

function argp_simple_translate_for_editing($Text,$Strict) {
  switch ($Text) {
    case "percent": return "%"; break;
    case "ldquo": return "``"; break;
    case "rdquo": return "''"; break;
    case "lsquo": return "`"; break;
    case "rsquo": return "'"; break;
    case "mdash": return "---"; break;
    case "ndash": return "--"; break;
    case "dollar": return "$"; break;
    case "and": return "&"; break;
    case "notappl": return "{@na}"; break;
    default:
      if ($strict)
        return "{@$Text}";
      return $Text;
  }
}

function argp_serialize_roll($PseudoXML,$STran) {
  $face = "";
  $num = "";
  $bns = "";
  $bOff = "";
  $rOff = "";
  $mul = "";
  $raw = "";
  $kind = "";
  foreach ($PseudoXML->childNodes as $rollparts) {
    $value = $STran($rollparts->nodeValue,false);
    switch ($rollparts->tagName) {
      case "face": $face = $value; break;
      case "num" : $num  = $value; break;
      case "bns" : $bns  = $value; break;
      case "bOff": $bOff = $value; break;
      case "raw" : $raw  = $value; break;
      case "rOff": $rOff = $value; break;
      case "mul" : $mul  = $value; break;
      case "kind": $kind = $value; break;
    }
  }
  return "{@roll $raw$rOff$num"."D$face$bOff$bns$mul$kind}";
}

function argp_serialize_elements_for_Q($PseudoXMLs,$STran) {
  $result = "";
  foreach ($PseudoXMLs as $child)
    if ($child->nodeType == XML_TEXT_NODE)
      $result .= $child->nodeValue;
    else if ($child->nodeType == XML_ELEMENT_NODE)
      switch ($child->tagName) {
        case "root":
          $result .= argp_serialize_elements_for_Q($child->childNodes,$STran);
          break;
        case "roll":
          $result .= argp_serialize_roll($child,$STran);
          break;
        default:
          $result .= $STran($child->tagName,true);
      }
  return $result;
}

function arpg_serialize_elements_for_display($Text) {
  $PseudoXMLstr = "<root>".$Text."</root>";
  $PseudoXML = new DOMDocument();
  $PseudoXML->loadXML($PseudoXMLstr);
  return argp_serialize_elements_for_Q($PseudoXML->childNodes,
    argp_simple_translate_for_display);
}

function arpg_serialize_elements_for_editing($Text) {
  $PseudoXMLstr = "<root>".$Text."</root>";
  $PseudoXML = new DOMDocument();
  $PseudoXML->loadXML($PseudoXMLstr);
  return argp_serialize_elements_for_Q($PseudoXML->childNodes,
    argp_simple_translate_for_editing);
}

function arpg_deserialize_elements_from_editing($Text) {
  return $Text;
}

function arpg_editable_text($Id,$Text) {
  $Text = arpg_serialize_elements_for_display($Text);
  $result = "<span onClick=\""
      .arpg_build_ajax("loader.php","ModifyText",
        $Id."@'+this.scrollWidth+':'+this.scrollHeight+'")
      ."\">".$Text."</span>";
  return $result;
}

function arpg_render_raw_text($Connection,$Id,$ExtraInfo) {
  if (!$ExtraInfo) $ExtraInfo = "";
  $result = array();
  $childNodes = xmldb_getChildNodes($Connection,$Id);
  $index = 0;
  foreach ($childNodes as $id => $child)
    if ($child["Kind"] === "element") {
      switch ($child["Name"]) {
      case "text":
        $result[$index] = "<p class='text' id='Text$id'>"
                          .arpg_editable_text($id,$child["Value"])
                          ."</p>";
        break;
      case "example":
        $mresult = arpg_render_raw_text($Connection,$id);
        $result[$index] = "<div class='example'>".implode("",$mresult)."</div>";
        break;
      case "title":
        $mresult = arpg_render_raw_text($Connection,$id);
        $result[$index] = "<h1>".implode("",$mresult)."</h1>";
        break;
      case "item":
        switch ($ExtraInfo) {
        case "description-list": // first one is dt, rest go into dl
          $mresult = arpg_render_raw_text($Connection,$id);
          $mrest = array_slice($mresult,1);
          $mbegin = arpg_render_raw_text($Connection,$mresult[0]);
          $result[$index] = "<dt>".$mresult[0]
                            ."</dt><dd>"
                            .implode("",$mrest)."</dd>";
          break;
        default:
          $mresult = arpg_render_raw_text($Connection,$id);
          $result[$index] = "<li>".implode("",$mresult)."</li>";
        }
        break;
      case "description":
        $result[$index] = implode("",arpg_render_raw_text($Connection,$id));
        break;
      case "numbered-list":
        $result[$index] = "<ol class='number-list'>";
        $mresult = arpg_render_raw_text($Connection,$id);
        $result[$index] .= implode("",$mresult);
        $result[$index] .= "</ol>";
        break;
      case "description-list":
        $result[$index] = "<dl class='description-list'>";
        $mresult = arpg_render_raw_text($Connection,$id,"description-list");
        $result[$index] .= implode("",$mresult);
        $result[$index] .= "</dl>";
        break;
      default:
        $result[$index] = "UNKNOWN@".$child["Name"];
      }
      $index++;
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
  $payloads = array("$text_value");
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
  if ($height < 3) $height = 3;


  $editable = "<table class='ModifyTextButton'><tbody><tr>";
  $editable .= "<td colspan='2'>";
  $editable .= "<textarea rows=$height cols=$width id='TA$text_id'>";
  $editable .= arpg_serialize_elements_for_editing($text);
  $editable .= "</textarea><br/>";
  $editable .= "</td></tr><tr>";
  $editable .= "<td><input type='button' value='Close' onClick=\""
                  .arpg_build_ajax("loader.php","UnmodifyText",$text_id)."\"
                  class='ModifyTextButton'/></td>";
  $editable .= "<td align='right'><input type='button' value='Update Database'
                  onClick=\"".arpg_build_ajax("loader.php","UpdateTextValue",
                                "<who>$text_id</who><what>'+"
                                ."document.getElementById('TA$text_id').value+'"
                                ."</what>"
                              )."\"
                  class='ModifyTextButton'/></td>";
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
  foreach ($titleVal as $id => $S) {
    $titleVal = $titleVal[$id];
    break;
  }

  $title_parts = arpg_render_raw_text($Connection,$title);
  $description_parts = arpg_render_raw_text($Connection,$description);

  $datum_responder = "<a onClick=\""
    .arpg_build_ajax("loader.php","UnloadDatum",$datum_id)
    ."\">".$titleVal["Value"]."</a>";

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
    $table_parts = arpg_render_raw_text($Connection,$id);
    $fields_response .= "<tr><td class='FieldResponderAspect' align='right'>"
                      .$allattrs[$id]["name"]["Value"]."</td><td>"
                      .implode("<br/>",$table_parts)."</td></tr>";
  }
  $fields_response .= "<tbody></table>";
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