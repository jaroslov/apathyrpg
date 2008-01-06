<?php

include "modify-common.php";

function arpg_unmodify_text($Response) {
  $Connection = arpg_create_apathy();
  $text_id = $Response->payload[0];
  $textNode = xmldb_getElementById($Connection,$text_id);
  $text = $textNode["Value"];

  $uneditable = arpg_render_inner_text($text_id,$text);

  $targets = array("Id$text_id");
  $payloads = array($uneditable);
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
  $width = $wh[0]-25;
  $height = $wh[1];

  $editable = "<div class='text-homonculus'>"
    ."<div class='homonculus-item' onclick=\""
    .arpg_build_ajax("modify-text.php","UnmodifyText",$text_id)
    ."\">Close</div>"
    ."<div class='homonculus-item' onclick=\""
    .arpg_build_ajax("modify-text.php","SaveChanges",
        "<who>$text_id</who><what>'+"
        ."xmlencode(document.getElementById('TA$text_id').value)+'"
        ."</what>")
    ."\">Save Changes</div>"
    ."</div>";
  $editable .= "<textarea id='TA$text_id' "
    ."style='min-height:".$height."px;height:100%;"
          ."min-width:".$width."px;width:100%;' >";
  $editable .= arpg_serialize_elements_for_editing($text);
  $editable .= "</textarea>";

  $targets = array("InTxt$text_id");
  $payloads = array($editable);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_save_changes($Response) {
  $Connection = arpg_create_apathy();
  $text_id = $Response->payload[0]->who[0];
  $text_value = $Response->payload[0]->what[0];

  $text_value = arpg_deserialize_elements_from_editing($text_value);

  xmldb_setNodeValueById($Connection,$text_id,$text_value);

  $targets = array("Log");
  $payloads = array(time().": ".$text_value);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_modify_text_responder() {
  $reply = $_GET["Message"];
  $replyXML = new SimpleXMLElement($reply);
  $targets = array();
  $payloads = array();

  foreach ($replyXML->response as $response) {
    $lres = array("Targets"=>array("Log"),
                  "Payloads"=>array("Unknown Code&#8658;".$response->code[0]));
    switch ($response->code[0]) {
    case "ModifyText":
      $lres = arpg_modify_text($response);
      break;
    case "UnmodifyText":
      $lres = arpg_unmodify_text($response);
      break;
    case "SaveChanges":
      $lres = arpg_save_changes($response);
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