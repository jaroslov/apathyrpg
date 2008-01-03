<?php

function arpg_get_environment() {
  $environment = array();
  $environment["Responder"] = $_GET["responder"];
  $environment["Target"] = $_GET["target"];
  $environment["Source"] = $_GET["source"];
  $environment["Code"] = $_GET["code"];
  $environment["Message"] = $_GET["message"];
  return $environment;
}

function arpg_encode_html ($html) {
  $html = str_replace("&","&amp;",$html);
  $html = str_replace("<","&lt;",$html);
  $html = str_replace(">","&gt;",$html);
  return $html;
}

function arpg_pseudo_html ($html) {
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

function arpg_depseudo_html ($html) {
  $html = str_replace("[;","<",$html);
  $html = str_replace("];",">",$html);
  $html = str_replace("&amp;[;","[",$html);
  $html = str_replace("&amp;];","]",$html);
  $html = str_replace("&amp;","&",$html);
  return $html;
}

function arpg_build_empty_response() {
  $targets = array();
  $payloads = array();
  return arpg_build_responses($targets, $payloads);
}

function arpg_build_response($target, $payload) {
  $targets = array();
  array_push($targets,$target);
  $payloads = array();
  array_push($payloads,$payload);
  return arpg_build_responses($targets, $payloads);
}

function arpg_build_responses($targets, $payloads) {
  $result = "<reply>";
  for ($idx = 0; $idx < sizeof($targets); $idx++) {
    $result .= "<response><target>".$targets[$idx]."</target>";
    $result .= "<payload>".arpg_encode_html($payloads[$idx])."</payload></response>";
  }
  $result .= "</reply>";
  return $result;
}

function arpg_make_ajax_function($event,$environment) {
  return $event."=\"ajaxFunction('".$environment["Responder"]."',"
                                  .$environment["Target"].","
                                  .$environment["Source"].","
                                  .$environment["Code"].","
                                  .$environment["Message"].")\"";
}

function arpg_make_select_statement($options,$environment) {
  $select = "<select class='MainChooser' id='".$environment["ID"]."' ";
  $select .= arpg_make_ajax_function("onChange",$environment);
  $select .= ">\n";
  if (is_array($options))
    foreach ($options as $option)
      $select .= $option."\n";
  else
    $select .= $options;
  $select .= "</select>";
  return $select;
}

function arpg_make_option_for_select($value,$content,$selected) {
  $option = "<option ";
  if ($selected)
    $option .= "selected ";
  $option .= "value='".$value."'>".$content."</option>";
  return $option;
}

function arpg_build_ajax($Responder,$Codes,$Payloads) {
  if (!is_array($Codes))
    $Codes = array($Codes);
  if (!is_array($Payloads))
    $Payloads = array($Payloads);
  $result = "<reply>";
  for ($cdx = 0; $cdx < sizeof($Codes); $cdx++)
    $result .= "<response><code>"
      .$Codes[$cdx]
      ."</code><payload>"
      .$Payloads[$cdx]
      ."</payload></response>";
  $result .= "</reply>";
  return "ajaxFunction('$Responder','$result')";
}

function argp_safe_response($Text) {
  //$Text = str_replace("<","[",$Text);
  //$Text = str_replace(">","]",$Text);
  return $Text;
}

?>