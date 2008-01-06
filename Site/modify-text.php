<?php

include "modify-common.php";

function arpg_modify_text($Response) {
  $Connection = arpg_create_apathy();
  $at_code = $Response->payload[0];
  $at_codes = explode("@",$at_code);
  $area = $at_codes[1];
  $text_id = $at_codes[0];
  $textNode = xmldb_getElementById($Connection,$text_id);

  $text = $textNode["Value"];

  $wh = explode(":",$area);
  $width = $wh[0] - 30;
  $height = $wh[1];

  $editable = "<div class='text-homonculus-long'>"
    ."<div class='homonculus-item'>Close</div>"
    ."<div class='homonculus-item'>Save Changes</div>"
    ."</div>";
  $editable .= "<textarea "
    ."style='height:".$height."px;width:".$width."px;margin-left:20px;' >";
  $editable .= arpg_serialize_elements_for_editing($text);
  $editable .= "</textarea>";

  $targets = array("InTxt$text_id");
  $payloads = array($editable);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_modify_text_responder() {
  $reply = $_GET["Message"];
  $replyXML = new SimpleXMLElement($reply);
  $targets = array();
  $payloads = array();

  foreach ($replyXML->response as $response) {
    $lres = array("Targets"=>array("Log"),
                  "Payloads"=>array("Unknown Code&rArr;".$response->code[0]));
    switch ($response->code[0]) {
    case "ModifyText":
      $lres = arpg_modify_text($response);
      break;
    }
    foreach ($lres["Targets"] as $target)
      array_push($targets,$target);
    foreach ($lres["Payloads"] as $payload)
      array_push($payloads,$payload);
  }
  return arpg_build_responses($targets,$payloads);
}

echo arpg_modify_text_responder();

?>