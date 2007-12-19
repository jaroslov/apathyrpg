<?php

$source = $_GET["source"];
$target = $_GET["target"];
$message = $_GET["message"];
$ApathyName = "Apathy.xml";
$Apathy = simplexml_load_file($ApathyName);

function encode_html ($html) {
  $html = str_replace("<","&lt;",$html);
  $html = str_replace(">","&gt;",$html);
  return $html;
}

function build_response ($target, $payload) {
  return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
      "<response><target>" .
      $target .
      "</target><payload>" .
      encode_html($payload) .
      "</payload></response>";
}

function get_categories($apathy,$within) {
  $result = array();
  if ($apathy === false) {
    array_push($result,"Failed to load the xml file.");
  } else {
    $categories = $apathy->{'raw-data'}->category;
    for ($idx = 0; $idx < sizeof($categories); $idx++) {
      $attrs = $categories[$idx]->attributes();
      foreach($attrs as $key => $value)
        if ($key === "name") {
          $subcats = explode("/",$value);
          for ($jdx = 0; $jdx < sizeof($subcats); $jdx++)
            if ($within === $subcats[$jdx])
              if (false === array_search($subcats[$jdx+1],$result))
                array_push($result,$subcats[$jdx+1]);
        }
    }
  }
  sort($result);
  $result = array_reverse($result);
  array_push($result,"---");
  return array_reverse($result);
}

function determine_response($from,$to,$msg,$apathy) {
  $parts = explode("/",$msg);
  if (sizeof($parts) > 1) {
    if ($parts[0] === "LoadCategory") {
      if ($parts[1] === "Choices") {
        $cats = get_categories($apathy,"Content");
        $res = "";
        for ($i=0; $i<sizeof($cats); $i++) {
          $res = $res . "<option value='LoadCategory/SubCat/Content/";
          $res = $res . $cats[$i];
          $res = $res . "'>" . $cats[$i] . "</option>";
        }
        return $res;
      } else if ($parts[1] === "SubCat") {
        $cats = get_categories($apathy,$parts[3]);
        $res = "<div id='".$parts[3].
          "'><select onChange=\"ajaxFunction(id,'".
          $parts[3]."Message',value)\">";
        for ($i=0; $i<sizeof($cats); $i++) {
          $nparts = array_values($parts);
          array_push($nparts, $cats[$i]);
          $res = $res . "<option value='";
          $res = $res . implode("/",$nparts);
          $res = $res . "'>" . $cats[$i] . "</option>";
        }
        return $res . "</select><p id='".$parts[3]."Message'>Message.</p></div>";
      }
    }
  }
  return "I don't know that message: ".$msg;
}

echo build_response($target, determine_response($source,$target,$message,$Apathy));

$handle = fopen($ApathyName, "w");
fwrite($handle, $Apathy->saveXML());
fclose($handle);

?>