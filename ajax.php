<?php

function get_environment() {
  $environment = array();
  $environment["Responder"] = $_GET["responder"];
  $environment["Target"] = $_GET["target"];
  $environment["Source"] = $_GET["source"];
  $environment["Code"] = $_GET["code"];
  $environment["Message"] = $_GET["message"];
  return $environment;
}

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
  $result = "<reply>";
  for ($idx = 0; $idx < sizeof($targets); $idx++) {
    $result .= "<response><target>".$targets[$idx]."</target>";
    $result .= "<payload>".encode_html($payloads[$idx])."</payload></response>";
  }
  $result .= "</reply>";
  return $result;
}

function make_ajax_function($event,$environment) {
  return $event."=\"ajaxFunction('".$environment["Responder"]."',"
                                  .$environment["Target"].","
                                  .$environment["Source"].","
                                  .$environment["Code"].","
                                  .$environment["Message"].")\"";
}

function make_select_statement($options,$environment) {
  $select = "<select class='MainChooser' ";
  $select .= make_ajax_function("onChange",$environment);
  $select .= ">\n";
  if (is_array($options))
    foreach ($options as $option)
      $select .= $option."\n";
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

?>