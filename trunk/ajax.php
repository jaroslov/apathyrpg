<?php

include 'apathy_xml.php';

$source = $_GET["source"];
$target = $_GET["target"];
$code = $_GET["code"];
$message = $_GET["message"];

function encode_html ($html) {
  $html = str_replace("&","&amp;",$html);
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
  $result = "<reply>";
  for ($idx = 0; $idx < sizeof($targets); $idx++) {
    $result .= "<response><target>".$targets[$idx]."</target>";
    $result .= "<payload>".encode_html($payloads[$idx])."</payload></response>";
  }
  $result .= "</reply>";
  return $result;
}

function text_click_response($trg,$src,$code,$msg,$apathy) {
  $msg_parts = explode("@",$msg);
  $unique_id = $msg_parts[0];
  $target = $msg_parts[1];
  $dimensions = $msg_parts[2];
  return text_click($apathy,$unique_id,$trg,$dimensions);
}

function make_ajax_function($event,$source,$target,$code,$message) {
  return $event."=\"ajaxFunction(".$source.","
                                  .$target.","
                                  .$code.","
                                  .$message.")\"";
}

function make_select_statement($options,$source,$target,$code,$message) {
  $select = "<select class='MainChooser' ";
  $select .= make_ajax_function("onChange",$source,$target,$code,$message);
  $select .= ">";
  if (is_array($options))
    foreach ($options as $option)
      $select .= $option;
  else
    $select .= $options;
  $select .= "</select>";
  return $select;
}

function make_option_for_select($value,$content,$selected) {
  $option = "<option ";
  if ($selected)
    $option .= "selected ";
  $option .= "value='".$value."'>".$content."</option>";
  return $option;
}

function make_arrow_path($parts) {
  $raquo = "&raquo;";
  $path = "<span class='CurrentPosition'>";
  if (is_array($parts))
    foreach ($parts as $part)
      $path .= $raquo . " " . $part . " ";
  else
    $path .= $parts;
  return $path."</span>";
}

function build_category_path($apathy,$path) {
  /*
    Nontrivial function: we have a list of categories where
    the organization is implicit rather than explicit. That is,
    the categories are held flat, but maintain a pointer into
    an explicit structure (the `name' attribute). The pointer
    is like a path:
    Content/Augmentations/Arm, Hand
    We need to collate everybody.
  */
  $categories = $apathy->getElementsByTagName("category");
  $msg = $path;
  $result = array();
  $cats = array();
  for ($cdx = 0; $cdx < $categories->length; $cdx++)
    array_push($cats,$categories->item($cdx));
  $path = explode("/",$path);
  $newpath = array();
  for ($pdx = 1; $pdx < sizeof($path); $pdx++) {
    array_push($newpath,$path[$pdx-1]);
    if (strpos($path[$pdx],"@") !== false) {
      $options = array();
      array_push($options,make_option_for_select($newmessage,"Choose...",false));
      $atparts = explode("@",$path[$pdx]);
      $bangparts = explode("!",$atparts[1]);
      $id = $bangparts[0];
      $newmessage = implode("/",$newpath)."/@".$id;
      $datumid = "";
      if (sizeof($bangparts) > 1)
        $datumid = $bangparts[1];
      $cat = $apathy->getElementById($id);
      $data = $cat->getElementsByTagName("datum");
      for ($ddx=0; $ddx<$data->length; $ddx++) {
        $datum = $data->item($ddx);
        $name = $datum->getAttribute("name");
        $did = $datum->getAttribute("xml:id");
        $selected = false;
        if ($did === $datumid)
          $selected = true;
        array_push($options,make_option_for_select($newmessage."!".$did,$name,$selected));
      }
      $select = make_select_statement($options,
        "'Path'","'Path'","'LoadDatum'","value");
      array_push($result,$select);
    } else {
      $options = array();
      array_push($options,make_option_for_select("Content/","Choose...",false));
      $newcats = array();
      $uniquenames = array();
      foreach ($cats as $category) {
        $name = explode("/",$category->getAttribute("name"));
        $keep = true;
        for ($kdx = 0; $kdx < $pdx; $kdx++)
          if ($name[$kdx] !== $path[$kdx])
            $keep = false;
        if ($keep) {
            if (!in_array($name[$pdx],$uniquenames)) {
              $selected = false;
              if ($name[$pdx] === $path[$pdx])
                $selected = true;
              $hasmore = "/@" . $category->getAttribute("xml:id");
              if (sizeof($name) > ($pdx+1))
                $hasmore = "/";
              $option = make_option_for_select(implode("/",$newpath)."/".$name[$pdx].$hasmore,
                            $id." ".$name[$pdx],$selected);
              array_push($options,$option);
              array_push($uniquenames,$name[$pdx]);
            }
            array_push($newcats,$category);
        }
      }
      $cats = $newcats;
      $select = make_select_statement($options,"'Path'","'Path'","'LoadCategory'","value");
      array_push($result,$select);
    }
  }
  return $result;
}

function build_category_heading($apathy,$msg) {
  $main_menu_get = make_main_menu("RawData");

  $path = array();
  $catsels = build_category_path($apathy,$msg);

  array_push($path,"Raw Data");
  foreach ($catsels as $catsel)
    array_push($path,$catsel);

  $path_get = make_arrow_path($path);
  $result = $main_menu_get . " " . $path_get;
  return $result;
}

function load_category_response($trg,$src,$code,$msg,$apathy) {
  $payloads = array();
  array_push($payloads,build_category_heading($apathy,$msg));
  array_push($payloads,"<em>Please select more specifically.</em>");
  $targets = array();
  array_push($targets,"Path");
  array_push($targets,"Datum");
  return build_responses($targets,$payloads);
}

function message_to_datum($apathy,$message) {
  $atparts = explode("@",$message);
  $category_datum = $atparts[1];
  $bangparts = explode("!",$category_datum);
  $category_id = $bangparts[0];
  $datum_id = $bangparts[1];
  return $apathy->getElementById($datum_id);
}

function get_name_of_datum($datum) {
  foreach ($datum->childNodes as $field)
    if ($field->tagName === "field")
      if (false !== $field->hasAttribute("title"))
        return translate_child_text($field);
  return "No name.";
}

function build_modifyable_text($tabindex,$node,$style) {
  if (!$style)
    $class="";
  $blurFocusResponse = " onFocus='focusStyle(this);'"
    ." onBlur='blurStyle(this);'";
  $onChange = " onChange=\"ajaxFunction('Log','Log','UpdateValue@"
    .$node->getAttribute("xml:id")."',value)\"";
  return "<textarea tabindex="
    .$tabindex.$blurFocusResponse.$onChange
    ."style='".$style."'"
    .">".translate_child_text($node)."</textarea>";
}

function build_datum_response($trg,$src,$code,$msg,$apathy) {
  $datum = message_to_datum($apathy,$msg);
  $name = $datum->getAttribute("name");
  $table = "<table class='ModifyDatumTable'>";
  $titlenode = null;
  $tablenodes = array();
  $descriptionnode = null;
  foreach ($datum->childNodes as $field)
    if ($field->tagName === "field") {
      if (false !== $field->hasAttribute("title"))
        $titlenode = $field;
      else if (false !== $field->hasAttribute("description"))
        $descriptionnode = $field;
      else if (false !== $field->hasAttribute("table"))
        array_push($tablenodes,$field);
    }
  $table .= "<tr><td></td><td align='center'>Aspect</td>"
    ."<td align='center'>Description</td></tr>";
  $title = $titlenode->getAttribute("name");
  $rows = sizeof($tablenodes)+1;
  $blurFocusResponse = " onFocus='focusStyle(this);'"
    ." onBlur='blurStyle(this);'";
  $onChange = " onChange=\"ajaxFunction('Log','Log','LogMessage',value)\"";
  $table .= "<tr><td align='right'>Name:&rsaquo;</td><td>"
    .build_modifyable_text(1,$titlenode)
    ."</td><td rowspan='".$rows."'>"
    .build_modifyable_text($rows+2,$descriptionnode,
      "width:30em;height:".(string)($rows*4)."em")
    ."</textarea></td></tr>";
  $tabindex = 1;
  foreach ($tablenodes as $tablenode) {
    $tabindex++;
    $name = $tablenode->getAttribute("name");
    $table .= "<tr><td align='right'><pre>".$name.":&rsaquo;</pre></td><td>";
    $table .= build_modifyable_text($tabindex,$tablenode);
    $table .= "</td></tr>";
  }
  $table .= "<tr><td></td><td colspan='2' align='right'>"
    ."<input id='SaveButton' style='width:15em;' "
    ."type='Button' value='Save'/></td></tr>";
  $table .= "</table>";
  return $table;
}

function load_datum_response($trg,$src,$code,$msg,$apathy) {
  $datum = message_to_datum($apathy,$msg);
  $name = "None";
  try {
    $name = get_name_of_datum($datum);
  } catch(Exception $e) {
    $name = $e->getMessage();
  }
  $payloads = array();
  array_push($payloads,$name);
  array_push($payloads,build_category_heading($apathy,$msg));
  array_push($payloads,build_datum_response($trg,$src,$code,$msg,$apathy));
  $targets = array();
  array_push($targets,"@title");
  array_push($targets,"Path");
  array_push($targets,"Datum");
  return build_responses($targets,$payloads);
}

function raw_data_response($trg,$src,$code,$msg,$apathy) {
  return load_category_response($trg,$src,$code,"Content/",$apathy);
}

function make_main_menu($which) {
  $options = array();
  $chsel = false;
  $bksel = false;
  $rdsel = false;
  switch ($which) {
    case "Choose": $chsel = true; break;
    case "Book": $bksel = true; break;
    case "RawData": $rdsel = true; break;
  }
  array_push($options,make_option_for_select("Initialize","Choose...",$chsel));
  array_push($options,make_option_for_select("NoResponse","Book",$bksel));
  array_push($options,make_option_for_select("RawData","Raw Data",$rdsel));
  return make_select_statement($options,"'Path'","'Path'","value","''");
}

function initialize_system($target,$source,$code,$message,$apathy) {
  $payloads = array();
  array_push($payloads,make_main_menu("Choose"));
  array_push($payloads,"<em>No data shown.</em>");
  $targets = array();
  array_push($targets,"Path");
  array_push($targets,"Datum");
  return build_responses($targets,$payloads);
}

function update_value_response($trg,$src,$code,$msg,$apathydom) {
  $apathy = $apathydom->ownerDocument;
  $atcodes = explode("@",$code);
  $code = $atcodes[0];
  $target_id = $atcodes[1];
  $node = $apathy->getElementById($target_id);
  $node->nodeValue = $msg;
  $sxml = simplexml_import_dom($apathy);
  $worked = $sxml->asXML("Apathy.tmp.xml");
  return build_response("Log",
    "<b>".$target_id
      ." &laquo;</b><span style='color:blue;'>"
      .$msg."</span><b>&raquo;</b>");
}

function respond($trg,$src,$code,$msg,$apathydom) {
  $apathy = $apathydom->ownerDocument;
  if ("Initialize" === $code) {
    return initialize_system($trg,$src,$code,$msg,$apathy);
  } else if ("Click:text" === $code) {
    return text_click_response($trg,$src,$code,$msg,$apathy);
  } else if ("NoResponse" === $code) {
    return build_empty_response();
  } else if ("RawData" === $code) {
    return raw_data_response($trg,$src,$code,$msg,$apathy);
  } else if ("LoadCategory" === $code) {
    return load_category_response($trg,$src,$code,$msg,$apathy);
  } else if ("LoadDatum" === $code) {
    return load_datum_response($trg,$src,$code,$msg,$apathy);
  } else if ("LogMessage" === $code) {
    return build_response("Log",
      "<b>\"</b><span style='color:green;'>".$msg."</span><b>\"</b>");
  } else {
    if (false !== strpos($code,"@")) {
      return update_value_response($trg,$src,$code,$msg,$apathydom);
    }
  }
  return build_response($trg,
      "<p>Not a known code:".$code
      ." with ".$trg."->".$src."@".$msg."<br/>With Dom: "
      .gettype($apathydom)."</p>");
}

echo respond($target,$source,$code,$message,get_apathy_dom());

?>