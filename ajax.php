<?php

$source = $_GET["source"];
$target = $_GET["target"];
$code = $_GET["code"];
$message = $_GET["message"];
$ApathyName = "Apathy.tmp.xml";
$ApathyXml = simplexml_load_file($ApathyName);
$ApathyDom = dom_import_simplexml($ApathyXml)->ownerDocument;

function encode_html ($html) {
  $html = str_replace("&","&amp;",$html);
  $html = str_replace("<","&lt;",$html);
  $html = str_replace(">","&gt;",$html);
  return $html;
}

function pseudo_html ($html) {
  $html = str_replace("<lsquo/>","&lsquo;",$html);
  $html = str_replace("<ldquo/>","&ldquo;",$html);
  $html = str_replace("<rsquo/>","&rsquo;",$html);
  $html = str_replace("<rdquo/>","&rdquo;",$html);
  $html = str_replace("&","&amp;",$html);
  $html = str_replace("[","&amp;[;",$html);
  $html = str_replace("]","&amp;];;",$html);
  $html = str_replace("<","[;",$html);
  $html = str_replace(">","];",$html);
  return $html;
}

function depseudo_html ($html) {
  $html = str_replace("[;","<",$html);
  $html = str_replace("];",">",$html);
  $html = str_replace("&amp;[;","[",$html);
  $html = str_replace("&amp;];","]",$html);
  $html = str_replace("&amp;","&",$html);
  return $html;
}

function build_empty_response() {
  $targets = array();
  $payloads = array();
  return build_responses($targets, $payloads);
}

function build_response($target, $payload) {
  $targets = array();
  array_push($targets,$target);
  $payloads = array();
  array_push($payloads,$payload);
  return build_responses($targets, $payloads);
}

function build_responses($targets, $payloads) {
  $result = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><reply>";
  for ($idx = 0; $idx < sizeof($targets); $idx++) {
    $result .= "<response><target>".$targets[$idx]."</target>";
    $result .= "<payload>".encode_html($payloads[$idx])."</payload></response>";
  }
  $result .= "</reply>";
  return $result;
}

function translate_child_text($node) {
  $text = "";
  foreach ($node->childNodes as $child)
    $text .= translate_text($child);
  return $text;
}

function translate_text($node) {
  if ("Apathy" === $node->tagName)
    return "{Apathy}";
  else if ("C" === $node->tagName)
    return "{C}";
  else if ("plusminus" === $node->tagName)
    return "&#177";
  else if ("and" === $node->tagName)
    return "&";
  else if ("dollar" === $node->tagName)
    return "$";
  else if ("percent" === $node->tagName)
    return "%";
  else if ("rightarrow" === $node->tagName)
    return "&#8594";
  else if ("ldquo" === $node->tagName)
    return "&ldquo;";
  else if ("lsquo" === $node->tagName)
    return "&lsquo;";
  else if ("rdquo" === $node->tagName)
    return "&rdquo;";
  else if ("rsquo" === $node->tagName)
    return "&rsquo;";
  else if ("mdash" === $node->tagName)
    return "&mdash;";
  else if ("ndash" === $node->tagName)
    return "&ndash;";
  else if ("ouml" === $node->tagName)
    return "&ouml;";
  else if ("oslash" === $node->tagName)
    return "&#248;";
  else if ("trademark" === $node->tagName)
    return "&#8482;";
  else if ("times" === $node->tagName)
    return "*";
  else if ("Sum" === $node->tagName)
    return "&#8721;";
  else if ("roll" === $node->tagName)
    return "{roll ".translate_child_text($node)."}";
  else if ("raw" === $node->tagName)
    return "[".translate_child_text($node)."]";
  else if ("rOff" === $node->tagName)
    return "{rOff ".translate_child_text($node)."}";
  else if ("num" === $node->tagName)
    return "{num ".translate_child_text($node)."}";
  else if ("face" === $node->tagName)
    return "{face ".translate_child_text($node)."}";
  else if ("bOff" === $node->tagName)
    return "{bOff ".translate_child_text($node)."}";
  else if ("bns" === $node->tagName)
    return "{bns ".translate_child_text($node)."}";
  else if ("mul" === $node->tagName)
    return "{mul ".translate_child_text($node)."}";
  else if ("kind" === $node->tagName)
    return "{kind ".translate_child_text($node)."}";
  else if ("notappl" === $node->tagName)
    return "{n/a}";
  else if ("define" === $node->tagName)
    return "{def".translate_text($node->childNodes)."}";
  else if ("crushing" === $node->tagName)
    return "crushing";
  else if ("math" === $node->tagName)
    return "{math ".translate_child_text($node)."}";
  else if ("mrow" === $node->tagName)
    return "{".translate_child_text($node)."}";
  else if ("mi" === $node->tagName)
    return "{".translate_child_text($node)."}";
  else if ("mo" === $node->tagName)
    return translate_child_text($node);
  else if ("mn" === $node->tagName)
    return translate_child_text($node);
  else if ("msup" === $node->tagName)
    return "{msup ".translate_child_text($node)."}";
  else if ("munderover" === $node->tagName)
    return "{munderover ".translate_child_text($node)."}";
  else if ("mfrac" === $node->tagName)
    return "{mfrac ".translate_child_text($node)."}";
  else if ("mstyle" === $node->tagName)
    return "{mstyle ".translate_child_text($node)."}";
  else if ("footnote" === $node->tagName)
    return "{footnote}";
  else if ($node->nodeType === 3)
    return $node->nodeValue;
  else
    return "{nodeType ".$node->nodevalue."@".$node->tagName.":".(string) $node->nodeType."}";
}

function text_click($apathy,$unique_id,$target,$dimensions) {
  $dimpts = explode(":",$dimensions);
  $height = $dimpts[0]-6;
  $width = $dimpts[1];
  $element = $apathy->getElementById($unique_id);
  $children = $element->childNodes;
  $text = "";
  foreach ($children as $child)
    $text .= translate_text($child);
  return build_response($target,
    "<table class='UpdateTable' style='width:100%'><tr><td>"
        ."<textarea style='height:".$height."px;width:"
          .$width."px;color:black;font-family:helvetica;'>"
          .$text
        ."</textarea>"
      ."</td></tr><td align='right'>"
      ."<input style='width:20em;' type='button' value='Save'"
        ." onClick=\"document.title='Save! ".$unique_id."@".$target."'\" />"
    ."</td></tr></table>");
}

function text_click_response($trg,$src,$code,$msg,$apathy) {
  $msg_parts = explode("@",$msg);
  $unique_id = $msg_parts[0];
  $target = $msg_parts[1];
  $dimensions = $msg_parts[2];
  return text_click($apathy,$unique_id,$trg,$dimensions);
}

function make_ajax_function($event,$source,$target,$code,$message) {
  return $event."=\"ajaxFunction(".$source.","
                                  .$target.","
                                  .$code.","
                                  .$message.")\"";
}

function make_select_statement($options,$source,$target,$code,$message) {
  $select = "<select class='MainChooser' ";
  $select .= make_ajax_function("onChange",$source,$target,$code,$message);
  $select .= ">";
  if (is_array($options))
    foreach ($options as $option)
      $select .= $option;
  else
    $select .= $options;
  $select .= "</select>";
  return $select;
}

function make_option_for_select($value,$content,$selected) {
  $option = "<option ";
  if ($selected)
    $option .= "selected ";
  $option .= "value='".$value."'>".$content."</option>";
  return $option;
}

function make_arrow_path($parts) {
  $raquo = "&raquo;";
  $path = "<span class='CurrentPosition'>";
  if (is_array($parts))
    foreach ($parts as $part)
      $path .= $raquo . " " . $part . " ";
  else
    $path .= $parts;
  return $path."</span>";
}

function build_category_path($apathy,$path) {
  /*
    Nontrivial function: we have a list of categories where
    the organization is implicit rather than explicit. That is,
    the categories are held flat, but maintain a pointer into
    an explicit structure (the `name' attribute). The pointer
    is like a path:
    Content/Augmentations/Arm, Hand
    We need to collate everybody.
  */
  $categories = $apathy->getElementsByTagName("category");
  $msg = $path;
  $result = array();
  $cats = array();
  for ($cdx = 0; $cdx < $categories->length; $cdx++)
    array_push($cats,$categories->item($cdx));
  $path = explode("/",$path);
  $newpath = array();
  for ($pdx = 1; $pdx < sizeof($path); $pdx++) {
    array_push($newpath,$path[$pdx-1]);
    if (strpos($path[$pdx],"@") !== false) {
      $options = array();
      array_push($options,make_option_for_select($newmessage,"Choose...",false));
      $atparts = explode("@",$path[$pdx]);
      $bangparts = explode("!",$atparts[1]);
      $id = $bangparts[0];
      $newmessage = implode("/",$newpath)."/@".$id;
      $datumid = "";
      if (sizeof($bangparts) > 1)
        $datumid = $bangparts[1];
      $cat = $apathy->getElementById($id);
      $data = $cat->getElementsByTagName("datum");
      for ($ddx=0; $ddx<$data->length; $ddx++) {
        $datum = $data->item($ddx);
        $name = $datum->getAttribute("name");
        $did = $datum->getAttribute("xml:id");
        $selected = false;
        if ($did === $datumid)
          $selected = true;
        array_push($options,make_option_for_select($newmessage."!".$did,$name,$selected));
      }
      $select = make_select_statement($options,
        "'Body'","'Body'","'LoadDatum'","value");
      array_push($result,$select);
    } else {
      $options = array();
      array_push($options,make_option_for_select("Content/","Choose...",false));
      $newcats = array();
      $uniquenames = array();
      foreach ($cats as $category) {
        $name = explode("/",$category->getAttribute("name"));
        $keep = true;
        for ($kdx = 0; $kdx < $pdx; $kdx++)
          if ($name[$kdx] !== $path[$kdx])
            $keep = false;
        if ($keep) {
            if (!in_array($name[$pdx],$uniquenames)) {
              $selected = false;
              if ($name[$pdx] === $path[$pdx])
                $selected = true;
              $hasmore = "/@" . $category->getAttribute("xml:id");
              if (sizeof($name) > ($pdx+1))
                $hasmore = "/";
              $option = make_option_for_select(implode("/",$newpath)."/".$name[$pdx].$hasmore,
                            $id." ".$name[$pdx],$selected);
              array_push($options,$option);
              array_push($uniquenames,$name[$pdx]);
            }
            array_push($newcats,$category);
        }
      }
      $cats = $newcats;
      $select = make_select_statement($options,"'Body'","'Body'","'LoadCategory'","value");
      array_push($result,$select);
    }
  }
  return $result;
}

function load_category_response($trg,$src,$code,$msg,$apathy) {
  $main_menu_get = make_main_menu("RawData");

  $path = array();
  $catsels = build_category_path($apathy,$msg);

  array_push($path,"Raw Data");
  foreach ($catsels as $catsel)
    array_push($path,$catsel);

  $path_get = make_arrow_path($path);
  $result = $main_menu_get . " " . $path_get;
  return build_response($trg,$result);
}

function load_datum_response($trg,$src,$code,$msg,$apathy) {
  return load_category_response($trg,$src,$code,$msg,$apathy);
}

function raw_data_response($trg,$src,$code,$msg,$apathy) {
  return load_category_response($trg,$src,$code,"Content/",$apathy);
}

function make_main_menu($which) {
  $options = array();
  $chsel = false;
  $bksel = false;
  $rdsel = false;
  switch ($which) {
    case "Choose": $chsel = true; break;
    case "Book": $bksel = true; break;
    case "RawData": $rdsel = true; break;
  }
  array_push($options,make_option_for_select("Initialize","Choose...",$chsel));
  array_push($options,make_option_for_select("NoResponse","Book",$bksel));
  array_push($options,make_option_for_select("RawData","Raw Data",$rdsel));
  return make_select_statement($options,"'Body'","'Body'","value","''");
}

function initialize_system($target,$source,$code,$message,$apathy) {
  return build_response($target,make_main_menu("Choose"));
}

function respond($trg,$src,$code,$msg,$apathy) {
  if ("Initialize" === $code) {
    return initialize_system($trg,$src,$code,$msg,$apathy);
  } else if ("Click:text" === $code) {
    return text_click_response($trg,$src,$code,$msg,$apathy);
  } else if ("NoResponse" === $code) {
    return build_empty_response();
  } else if ("RawData" === $code) {
    return raw_data_response($trg,$src,$code,$msg,$apathy);
  } else if ("LoadCategory" === $code) {
    return load_category_response($trg,$src,$code,$msg,$apathy);
  } else if ("LoadDatum" === $code) {
    return load_datum_response($trg,$src,$code,$msg,$apathy);
  } else
    return build_response($trg,"<p>Not a known code:".$code
      ." with ".$trg."->".$src."@".$msg."</p>");
}

echo respond($target,$source,$code,$message,$ApathyDom);

$handle = fopen($ApathyName, "w");
fwrite($handle, $Apathy->saveXML());
fclose($handle);

?>