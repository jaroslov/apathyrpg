<?php

$source = $_GET["source"];
$target = $_GET["target"];
$message = $_GET["message"];
$ApathyName = "Apathy.xml";
$Apathy = simplexml_load_file($ApathyName);

function encode_html ($html) {
  $html = str_replace("&","&amp;",$html);
  $html = str_replace("<dollar/>","$",$html);
  $html = str_replace("<","&lt;",$html);
  $html = str_replace(">","&gt;",$html);
  return $html;
}

function pseudo_html ($html) {
  $html = str_replace("&","&amp;",$html);
  $html = str_replace("[","&amp;[;",$html);
  $html = str_replace("]","&amp;];;",$html);
  $html = str_replace("<","[;",$html);
  $html = str_replace(">","];",$html);
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

function get_categories($apathy,$path) {
  $result = array();
  if ($apathy === false) {
    array_push($result,"Failed to load the xml file.");
  } else {
    $categories = $apathy->{'raw-data'}->category;
    for ($idx = 0; $idx < sizeof($categories); $idx++) {
      $attrs = $categories[$idx]->attributes();
      foreach($attrs as $key => $value)
        if ($key === "name") {
          if (false !== strpos($value, $path)) {
            $vparts = explode("/",$value);
            $pparts = explode("/",$path);
            $npath = implode("/",array_slice($vparts,0,sizeof($pparts)+1));
            if (false === in_array($npath, $result))
              array_push($result, $npath);
          }
        }
    }
  }
  sort($result);
  return $result;
}

function find_category($apathy,$path) {
  $categories = $apathy->{'raw-data'}->category;
  $category = false;
  for ($idx = 0; $idx < sizeof($categories); $idx++) {
    $attrs = $categories[$idx]->attributes();
    foreach ($attrs as $key => $value)
      if ($key === "name")
        if (false !== strpos($value, $path))
          $category = $categories[$idx];
  }
  return $category;
}

function find_datum($category,$name) {
  for ($idx = 0; $idx < sizeof($category->datum); $idx++)
    if ($name === (string) $category->datum[$idx]->field[0])
      return $category->datum[$idx];
  return false;
}

function display_datum($apathy,$path) {
  $pathparts = explode("@",$path);
  $datumname = $pathparts[0];
  $categorypath = $pathparts[1];
  $position = (int) $pathparts[2];
  $category = find_category($apathy,$categorypath);
  $datum = $category->datum[$position];
  if (false === $datum) {
    return "<p>Could not find: ".$path."</p>";
  }
  $displaytable = "<table id='DatumDisplayTable' class='DisplayForm'>";
  $tablepartname = array();
  $tablepartform = array();
  $description = "";
  $tablepartheight = 5;
  for ($idx = 0; $idx < sizeof($datum->field); $idx++) {
    $attrs = $datum->field[$idx]->attributes();
    $kind = "table";
    foreach ($attrs as $key => $value) {
      if ($key === "name") {
        if (!$attrs["description"])
          array_push($tablepartname, (string) $value);
      }
      if ($key === "description") {
        if ("yes" === (string) $value)
          $kind = "description";
      } else {
        if ($key === "title")
          if ("yes" === (string) $value)
            $kind = "title";
      }
    }
    if ($kind === "title" or $kind === "table") {
      $tablep = "<textarea style='width:35em;height:"
        .(string) $tablepartheight."em;' rows=\"1\">";
      $tablep .= (string) $datum->field[$idx]->asXML();
      $tablep .= "</textarea>";
      array_push($tablepartform,$tablep);
      $input .= $tablep;
    } else if ($kind === "description") {
      $description = "<textarea style='width:35em;height:%HEIGHT%;'>";
      $dom = dom_import_simplexml($datum->field[$idx]);
      $dom->hasChildNodes();
      $description .= (string) $datum->field[$idx]->asXML();
      $description .= "</textarea>";
    }
  }
  if (sizeof($tablepartform) > 0) {
    $description = str_replace("%HEIGHT%",
      (string)(sizeof($tablepartform)*$tablepartheight-1)."em",$description);
    $displaytable .= "<tr><td><p align='right'>".$tablepartname[0]
      ."</p></td><td>".$tablepartform[0]."</td><td rowspan=\"".
      (string) sizeof($tablepartform)."\">"
      ."Description<br/>".$description."</td></tr>";
  }
  if (sizeof($tablepartform) > 1) {
    for ($idx = 1; $idx < sizeof($tablepartform); $idx++)
      $displaytable .= "<tr><td><p align='right'>".$tablepartname[$idx]
        ."</p></td><td>".$tablepartform[$idx]."</td><td></td></tr>";
  }
  $displaytable .= "</table>";
  return "<br/>" . $displaytable;
}

function show_category($apathy,$path) {
  $result = array();
  if ($apathy === false) {
    return "<p>Failed to load the xml file.</p>";
  } else {
    $category = find_category($apathy,$path);
    $select = "<select style='width:20em;' ";
    $select .= "onChange=\"ajaxFunction(id,'Display',value)\">";
    $select .= "<option value='None'>Choose...</option>";
    for ($idx = 0; $idx < sizeof($category->datum); $idx++) {
      $name = (string)$category->datum[$idx]->field[0];
      $select .= "<option value='Display:".$name."@".$path."@".(string) $idx."'>";
      $select .= (string)$category->datum[$idx]->field[0];
      $select .= "</option>";
    }
    $select .= "</select>";
    $result = "<div id='Display'>";
    $result .= "</div>";
    return "&raquo; " . $select . $result;
  }
}

function load_category($apathy,$path) {
  $cats = get_categories($apathy,$path);
  $res = "<select style='width:20em;' ";
  $res .= "onChange=\"ajaxFunction('body','Body',value)\">";
  $res .= "<option value='None'>Choose...</option>";
  $res .= "<option value='LoadCategory:";
  $pathp = explode("/",$path);
  $uppath = implode("/",array_slice($pathp,0,sizeof($pathp)-1));
  if (false === strpos($uppath, "Content"))
    $uppath = "Content";
  $res .= $uppath;
  $backto = "";
  if (sizeof($pathp) > 1)
    $backto = $pathp[sizeof($pathp)-2];
  else
    $backto = "Content";
  $res .= "'>&laquo; ".$backto."</option>";
  for ($i=0; $i<sizeof($cats); $i++) {
    $res .= "<option value='LoadCategory:";
    $res .= $cats[$i];
    $name = explode("/",$cats[$i]);
    $res .= "'>" . $name[sizeof($name)-1] . "</option>";
  }
  $res .= "</select>";
  $curpos = "Apathy";
  for ($idx = 1; $idx < sizeof($pathp); $idx++)
    $curpos = $curpos . " &raquo; " . $pathp[$idx];
  $result = $res . " &raquo; " . $curpos . " ";
  if (1 === sizeof($cats))
    $result = $result . show_category($apathy,$path);
  return $result;
}

function determine_response($from,$to,$msg,$apathy) {
  $parts = explode(":",$msg);
  if (sizeof($parts) > 1) {
    if ($parts[0] === "LoadCategory") {
      return load_category($apathy,$parts[1]);
    } else if ($parts[0] === "Display") {
      return display_datum($apathy,$parts[1]);
    }
  }
  return "<p>I don't know that message: ".$msg."</p>";
}

echo build_response($target, determine_response($source,$target,$message,$Apathy));

$handle = fopen($ApathyName, "w");
fwrite($handle, $Apathy->saveXML());
fclose($handle);

?>