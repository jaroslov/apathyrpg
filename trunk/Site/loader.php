<?php

include "arpg.php";
include "ajax.php";

function arpg_ajax_collated_categories($Categories) {
  function init($Cats,$CurPath)
    { return "<ol class='CategorySelector'>"; };
  function before($path,$CurPath)
    { return "<li><a>".$path."</a>"; };
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