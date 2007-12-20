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

function translate_text($node) {
  if ("Apathy" === $node->tagName)
    return "{Apathy}";
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
  else if ("crushing" === $node->tagName)
    return "{def crushing}";
  else if ("math" === $node->tagName)
    return "{math }";
  else if ($node->nodeType === 3)
    return $node->nodeValue;
  return "{nodeType ".(string) $node->nodeType."}";
}

function text_click($apathy,$unique_id,$target) {
  $element = $apathy->getElementById($unique_id);
  $children = $element->childNodes;
  $text = "";
  foreach ($children as $child)
    $text .= translate_text($child);
  return build_response($target,"<b style='color:green'>".$text."</b>");
}

function respond($trg,$src,$code,$msg,$apathy) {
  if ("Click:text" === $code) {
    $msg_parts = explode("@",$msg);
    $unique_id = $msg_parts[0];
    $target = $msg_parts[1];
    return text_click($apathy,$unique_id,$target);
  }
  return build_response($trg,"<p>Not a known code:".$code
    ." with ".$trg."->".$src."@".$msg."</p>");
}

echo respond($target,$source,$code,$message,$ApathyDom);

$handle = fopen($ApathyName, "w");
fwrite($handle, $Apathy->saveXML());
fclose($handle);

?>