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

function respond($trg,$src,$code,$msg,$apathy) {
  if ("Click:text" === $code) {
    $msg_parts = explode("@",$msg);
    $unique_id = $msg_parts[0];
    $target = $msg_parts[1];
    $dimensions = $msg_parts[2];
    return text_click($apathy,$unique_id,$trg,$dimensions);
  }
  return build_response($trg,"<p>Not a known code:".$code
    ." with ".$trg."->".$src."@".$msg."</p>");
}

echo respond($target,$source,$code,$message,$ApathyDom);

$handle = fopen($ApathyName, "w");
fwrite($handle, $Apathy->saveXML());
fclose($handle);

?>