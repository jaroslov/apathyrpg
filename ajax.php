<?php

$source = $_GET["source"];
$target = $_GET["target"];
$code = $_GET["code"];
$message = $_GET["message"];
$ApathyName = "Apathy.tmp.xml";
$Apathy = simplexml_load_file($ApathyName);

function encode_html ($html) {
  $html = str_replace("<lsquo/>","&lsquo;",$html);
  $html = str_replace("<ldquo/>","&ldquo;",$html);
  $html = str_replace("<rsquo/>","&rsquo;",$html);
  $html = str_replace("<rdquo/>","&rdquo;",$html);
  $html = str_replace("&","&amp;",$html);
  $html = str_replace("<dollar/>","$",$html);
  $html = str_replace("<Apathy/>","Apathy",$html);
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

function make_textarea($contents,$width,$height,$Id,$target) {
  $result= "<textarea class='StdTextArea' id=".$Id.""
    ."' onChange=\"ajaxFunction(".$Id.",".$Id.",'ChangeValue:"."',value)\""
    ." style='width:".$width.";height:".$height.";'>"
    .$contents
    ."</textarea>";
  //return $result;
  return "<textarea>".$Id."@".$target."</textarea>";
}

function render_as_editable($position,$width,$height) {
  $attrs = $position->attributes();
  foreach ($attrs as $key => $value)
    if ("unique-id" === (string) $value)
      $uid = (string) $value;
  if ("book" === (string) $position->getName()) {
    $parts = $position->part;
    $result = "<br /><ol class=\"RomanList\">";
    foreach ($parts as $v => $part) {
      $result .= "<li>".load_all_book($part,$width,$height)."</li>";
    }
    $result .= "</ol>";
    return $result;
  } else if ("part" === (string) $position->getName()) {
    $result = "<div class='BookStyled'>";
    $result .= make_textarea((string)$position->title[0],
      $width,"3em",$uid,"this.id");
    $result .= "<ol class=\"RomanList\">";
    foreach ($position->chapter as $v => $chapter) {
      $result .= "<li>".load_all_book($chapter,$width,$height)."</li>";
    }
    $result .= "</ol>";
    $result .= "</div>";
    return $result;
  } else if ("chapter" === (string) $position->getName()) {
    $result = "<div class='BookStyled'>";
    $result .= make_textarea((string) $position->title[0],
      $width,"3em",$uid,"this.id");
    $result .= "<ol class=\"RomanList\">";
    foreach ($position->section as $v => $section) {
      $result .= "<li>".load_all_book($section,$width,$height)."</li>";
    }
    $result .= "</ol>";
    $result .= "</div>";
    return $result;
  } else if ("section" === (string) $position->getName()) {
    $result = "<div class='BookStyled'>";
    $result .= make_textarea((string) $position->title[0],
      $width,"3em",$uid,"this.id");
    $result .= "<ol class=\"RomanList\">";
    foreach ($position->children() as $v => $child) {
      if ("title" !== $child->getName())
        $result .= "<li>".load_all_book($child)."</li>";
    }
    $result .= "</ol>";
    $result .= "</div>";
    return $result;
  } else if ("text" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("itemized-list" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("numbered-list" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("description-list" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("figure" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("note" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("equation" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("example" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("reference" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("table" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("summarize" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,$uid,"this.id");
  } else if ("field" === (string) $position->getName()) {
    return make_textarea((string) $position->asXML(),
      $width,$height,$uid,"this.id");
  }
  return "What is: " . (string) $position->getName();
}

function make_option_for_select($value,$message,$selected) {
  $result = "<option";
  if ($selected)
    $result .= " selected";
  $result .= " value='".$value."'>".$message."</option>";
  return $result;
}

function location_path($path) {
  $result = "";
  if (is_array($path)) {
    if (sizeof($path) > 0)
      $result .= location_path($path[0]);
    for ($pdx = 1; $pdx < sizeof($path); $pdx++)
      $result .= " <span class=''>&raquo;</span> " . (string) $path[$pdx];
  } else {
    $result .= (string) $path;
  }
  return $result;
}

function get_attribute ($element,$which) {
  foreach ($element->attributes() as $key => $value)
    if ($which === (string) $key)
      return (string) $value;
  return "";
}

function make_chooser_path($apathy,$path) {
  $selects = array();
  $pathparts = explode("/",$path);
  $element = $apathy;
  $newpath = array();
  foreach ($pathparts as $pdx => $pathpart) {
    $nelement = $element->children()[$pathpart];
    $select = "<select class='MainChooser'>";
    $select .= make_option_for_select("NoResponse","Choose...",false);
    $select .= make_option_for_select("NoResponse",(string) $pathpart,false);
    foreach ($nelement->children() as $child) {
      $tagname = $child->getName();
      if ($tagname === "part" or $tagname === "chapter"
        or $tagname === "section" or $tagname === "book") {
        $title = (string) $child->title;
        $select .= make_option_for_select("",$title,false);
      }
    }
    $select .= "</select>";
    array_push($selects,$select);
  }
  return location_path($selects);
}

function initial_load($trg,$src,$code,$msg,$apathy) {
  return build_response("Body", make_chooser_path($apathy,$msg));
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

function respond($trg,$src,$code,$msg,$apathy) {
  if ("Initialize" === $code) {
    $result = initial_load($trg,$src,$code,$msg,$apathy);
    if (false !== $result)
      return $result;
  } else if ("ChoosePath" === $code) {
    $result = initial_load($trg,$src,$code,$msg,$apathy);
    if (false !== $result)
      return $result;
  } else if ("NoResponse" === $code) {
    return build_empty_response();
  }
  return build_response($trg,"<p>Not a known code:".$code."</p>");
}

echo respond($target,$source,$code,$message,$Apathy);

$handle = fopen($ApathyName, "w");
fwrite($handle, $Apathy->saveXML());
fclose($handle);

?>