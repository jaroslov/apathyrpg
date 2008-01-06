<?php

include "modify-common.php";

function arpg_unmodify_text($Response) {
  $Connection = arpg_create_apathy();
  $text_id = $Response->payload[0];
  $textNode = xmldb_getElementById($Connection,$text_id);
  $text = $textNode["Value"];

  $uneditable = arpg_render_inner_text($text_id,$text);

  $targets = array("Id$text_id","Editor-Title","Editor-Body");
  $payloads = array($uneditable,"Editor","&#160");
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_save_changes($Response) {
  $Connection = arpg_create_apathy();
  $text_id = $Response->payload[0]->who[0];
  $text_value = $Response->payload[0]->what[0];

  $text_value = arpg_deserialize_elements_from_editing($text_value);
  xmldb_setNodeValueById($Connection,$text_id,$text_value);

  $textNode = xmldb_getElementById($Connection,$text_id);
  $text = $textNode["Value"];
  $uneditable = arpg_render_inner_text($text_id,$text);

  $targets = array("Log","Id$text_id");
  $payloads = array(time().": ".$text_value,$uneditable);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function arpg_modify_text($Response) {
  $Connection = arpg_create_apathy();
  $text_id = $Response->payload[0];
  $textNode = xmldb_getElementById($Connection,$text_id);
  $text = $textNode["Value"];

  $editable = "<div class='Edit-Controls'>"
    ."<div class='Edit-TD' onclick=\""
    .arpg_build_ajax("modify-text.php","UnmodifyText",$text_id)
    ."\">Close</div>"
    ."<div class='Edit-TD' onclick=\""
    .arpg_build_ajax("modify-text.php","SaveChanges",
        "<who>$text_id</who><what>'+"
        ."xmlencode(document.getElementById('TA$text_id').value)+'"
        ."</what>")
    ."\">Save Changes</div>"
    ."<div class='Edit-TD'>"
    .   "<ul class='MainMenu'><li>Structure..."
    .      "<ul class='Menu'>
              <li>Append...
                <ul class='Menu'>
                  <li>Text...
                    <ul class='Menu'>
                      <li>Paragraph</li>
                      <li>Note</li>
                      <li>Equation</li>
                    </ul>
                  </li>
                  <li>List...
                    <ul class='Menu'>
                      <li>Description</li>
                      <li>Numbered</li>
                      <li>Itemized</li>
                    </ul>
                  </li>
                  <li>Table</li>
                  <li>Section</li>
                </ul>
              </li>
              <li>Move...
                <ul class='Menu'>
                  <li>Up</li>
                  <li>Down</li>
                  <li>To top</li>
                  <li>To bottom</li>
                </ul>
              </li>
              <li>Remove</li>
            </ul>"
    .   "</li></ul>"
    ."</div>"
    ."</div>";
  $editable .= "<textarea id='TA$text_id'>";
  $editable .= arpg_serialize_elements_for_editing($text);
  $editable .= "</textarea>";

  $targets = array("Editor-Body","Editor-Title");
  $payloads = array($editable,
    "Editing: <a class='Editor-Reference' href='#Id$text_id'>#$text_id</a>");
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