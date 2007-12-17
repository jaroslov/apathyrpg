<?php

$source = $_GET["source"];
$target = $_GET["target"];
$message = $_GET["message"];

function build_response ($target, $payload) {
  return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
      "<response><target>" .
      $target .
      "</target><payload>" .
      $payload .
      "</payload></response>";
}

echo build_response($target, $source . " says " . $message);

?>