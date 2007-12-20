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
    array_push($result,"Failed to load the xml file; make sure \"Apathy.tmp.xml\" exists.");
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

function get_datum($apathy,$path) {
  $pathparts = explode("@",$path);
  $datumname = $pathparts[0];
  $categorypath = $pathparts[1];
  $position = (int) $pathparts[2];
  $category = find_category($apathy,$categorypath);
  return $category->datum[$position];
}

function display_datum($apathy,$path) {
  $datum = get_datum($apathy,$path);
  if (false === $datum) {
    return "<p>Could not find: ".$path."</p>";
  }
  $displaytable = "<table id='DatumDisplayTable' class='DisplayForm'>";
  $tablepartname = array();
  $tablepartform = array();
  $description = "";
  $tablepartheight = 5;
  $fieldname = "";
  for ($idx = 0; $idx < sizeof($datum->field); $idx++) {
    $attrs = $datum->field[$idx]->attributes();
    $kind = "table";
    foreach ($attrs as $key => $value) {
      if ($key === "name") {
          array_push($tablepartname, (string) $value);
      }
      if ($key === "description") {
        if ("yes" === (string) $value)
          $kind = "description";
      } else {
        if ($key === "title")
          if ("yes" === (string) $value) {
            $kind = "title";
            $fieldname = (string) $datum->field[$idx];
          }
      }
    }
    if ($kind === "title" or $kind === "table") {
      $tablep = render_as_editable($datum->field[$idx],
        "35em",(string) $tablepartheight."em");
      array_push($tablepartform,$tablep);
    } else if ($kind === "description")
      $description = $datum->field[$idx];
  }
  //return "<p>FOO</p>";
  if (sizeof($tablepartform) > 0) {
    $height = sizeof($tablepartform)*$tablepartheight;
    $displaytable .= "<tr><td></td><td>"
      ."<p align='center' class='TableLabel'>Aspects</p></td><td>"
      ."<p align='center' class='TableLabel'>Description</p></td></tr>";
    $displaytable .= "<tr><td><p align='right' class='TableLabel'>"
      .$tablepartname[0]
      .":&rsaquo;</p></td><td>".$tablepartform[0]."</td><td rowspan=\"".
      (string) sizeof($tablepartform)."\">"
      .render_as_editable($description,"35em",(string)$height."em")
      ."</td></tr>";
  }
  if (sizeof($tablepartform) > 1) {
    for ($idx = 1; $idx < sizeof($tablepartform); $idx++)
      $displaytable .= "<tr><td><p align='right' class='TableLabel'>"
        .$tablepartname[$idx]
        .":&rsaquo;</p></td><td>".$tablepartform[$idx]."</td><td></td></tr>";
  }
  $displaytable .= "<tr><td></td><td colspan='2'>";
  $displaytable .= "<p align='center'><input style='width:40em;' "
    ."type='button' onClick=\"\" value='Save changes to \""
    //.(string) $fieldname."\"'/></p>";
    .(string) $path."\"'/></p>";
  $displaytable .= "</tr>";
  $displaytable .= "</table>";
  return "<br/>" . $displaytable;
}

function show_category($apathy,$path) {
  $result = array();
  if ($apathy === false) {
    return "<p>Failed to load the xml file. Make sure \"Apathy.tmp.xml\" exists.</p>";
  } else {
    $category = find_category($apathy,$path);
    $select = "<select style='width:20em;' ";
    $select .= "onChange=\"ajaxFunction(id,'Display','Display',value)\">";
    $select .= "<option value='NoResponse'>Choose...</option>";
    for ($idx = 0; $idx < sizeof($category->datum); $idx++) {
      $name = (string)$category->datum[$idx]->field[0];
      $select .= "<option value='".$name."@".$path."@".(string) $idx."'>";
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
  $res = "<select class='Chooser' ";
  $res .= "onChange=\"ajaxFunction('body','Body','LoadCategory',value)\">";
  $res .= "<option value='NoResponse'>Choose...</option>";
  if ($path !== "Content") {
    $res .= "<option value='";
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
    $res .= "'>&lsaquo; ".$backto."</option>";
  }
  for ($i=0; $i<sizeof($cats); $i++) {
    $res .= "<option value='";
    $res .= $cats[$i];
    $name = explode("/",$cats[$i]);
    $res .= "'>" . $name[sizeof($name)-1] . "</option>";
  }
  $res .= "</select>";
  $curpos = "<span class='CurrentPosition'>Apathy Raw-Data";
  for ($idx = 1; $idx < sizeof($pathp); $idx++)
    $curpos = $curpos . " &raquo; " . $pathp[$idx];
  $result = $res . " &raquo; " . $curpos . "</span> ";
  if (1 === sizeof($cats))
    $result = $result . show_category($apathy,$path);
  return load_apathy() . " " . $result;
}

function make_textarea($contents,$width,$height,$Id,$target) {
  $result= "<textarea class='StdTextArea' id="
    .$Id.""
//    ."' onKeyUp=\"ajaxFunction(id,"
//    .$target
//    .",'ChangeValue:"
//    ."',value)\""
    ." style='width:"
    .$width
    .";height:"
    .$height
    .";'>"
    .$contents
    ."</textarea>";
  return $result;
}

function render_as_editable($position,$width,$height) {
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
      $width,"3em","'None'","this.id");
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
      $width,"3em","'None'","this.id");
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
      $width,"3em","'None'","this.id");
    $result .= "<ol class=\"RomanList\">";
    foreach ($position->children() as $v => $child) {
      if ("title" !== $child->getName())
        $result .= "<li>".load_all_book($child)."</li>";
    }
    $result .= "</ol>";
    $result .= "</div>";
    return $result;
  } else if ("text" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("itemized-list" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("numbered-list" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("description-list" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("figure" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("note" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("equation" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("example" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("reference" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("table" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("summarize" === (string) $position->getName()) {
    return make_textarea($position->asXML(),$width,$height,"'None'","this.id");
  } else if ("field" === (string) $position->getName()) {
    $uniquename = "'None'";
    foreach ($position->attributes() as $key => $value)
      if ("name" === (string) $key)
        $uniquename = (string) $value;
    return make_textarea((string) $position->asXML(),
      $width,$height,(string)$uniquename,"id");
  }
  return "What is: " . (string) $position->getName();
}

function load_all_book($position) {
  return render_as_editable($position,"80%","12em");
}

function load_book($apathy) {
  $result = load_apathy() . " &raquo; Apathy Book";
  $result .= load_all_book($apathy->book);
  return $result;
}

function load_apathy() {
  $select = "<select class='MainChooser'"
    ." onChange=\"ajaxFunction(id,'Body','InitialLoad',value)\">";
  $select .= "<option value='LoadApathy'>Choose...</option>";
  $select .= "<option value='LoadBook:Book'>Book</option>";
  $select .= "<option value='LoadCategory:Content'>Raw Data</option>";
  $select .= "</select>";
  return $select;
}

function determine_response($from,$to,$code,$msg,$apathy) {
  if ("InitialLoad" === $code) {
    if ("LoadApathy" === $msg)
      return load_apathy();
    $parts = explode(":",$msg);
    if (sizeof($parts) > 1) {
      if ($parts[0] === "LoadCategory") {
        return load_category($apathy,$parts[1]);
      } else if ($parts[0] === "Display") {
        return display_datum($apathy,$parts[1]);
      }
      if ("LoadBook" === $parts[0]) {
        return load_book($apathy,$parts[1]);
      }
    }
  } else if ("LoadCategory" == $code) {
    return load_category($apathy,$msg);
  } else if ("Display" == $code) {
    return display_datum($apathy,$msg);
  }
  if ("NoResponse" === $msg)
    return "<p>&nbsp;</p>";
  $parts = explode(":",$code);
  if ("ChangeValue" === $parts[0]) {
    $bits = explode("/",$parts[1]);
    return "@".encode_html($msg);
    //return make_textarea($bits[0],$bits[1],$bits[2],$bits[3],encode_html($msg));
  }
  return "I don't know that message: ".pseudo_html($code."@".$msg)."";
}

echo build_response($target,
  determine_response($source,$target,$code,$message,$Apathy));

$handle = fopen($ApathyName, "w");
fwrite($handle, $Apathy->saveXML());
fclose($handle);

?>