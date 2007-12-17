<?php

$message = $_GET["username"];

function build_response ($target, $payload) {
  return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
      "<response><target>" .
      $target .
      "</target><payload>" .
      $payload .
      "</payload></response>";
}

echo build_response("time",$message);

?>