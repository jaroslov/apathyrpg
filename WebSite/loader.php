<?php

include "ajax.php";
include "populate_apathy.php";

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
  array_push($options,arpg_make_option_for_select("RebuildMainMenu","Choose...",$chsel));
  array_push($options,arpg_make_option_for_select("Book","Book",$bksel));
  array_push($options,arpg_make_option_for_select("RawData","Raw Data",$rdsel));
  $env = array("Responder"=>"loader.php",
               "Target"=>"'Path'",
               "Source"=>"'Path'",
               "Code"=>"value",
               "Message"=>"''");
  return arpg_make_select_statement($options,$env);
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

function load_category_path($environment,$WhichDatum) {
  $catparts = array();
  $catnames
    = xmldb_getAttributeOfTagName($environment["Connection"],"category","name");
  $path = explode("/",$environment["Message"]);
  $pathlen = sizeof($path);
  $options = array();

  // buld the "Choose..." and "Go up a level" options
  array_push($options,arpg_make_option_for_select("NoResponse","Choose...",true));
  if (sizeof($path) > 1) {
    $npath = array();
    for ($pdx = 0; $pdx < $pathlen-1; $pdx++)
      array_push($npath,$path[$pdx]);
    array_push($options,
      arpg_make_option_for_select(implode("/",$npath),"&laquo; ".$path[$pathlen-2],false));
  } else {
    array_push($options,
      arpg_make_option_for_select("Content","&laquo; Content",false));
  }

  // first, do we have an exact match?
  $exact = null;
  $datum_select = null;
  $WhichCat = null;
  foreach ($catnames as $catname)
    if ($catname["Value"] === $environment["Message"]) {
      $exact = $catname;
      $WhichCat = $path[$pathlen-1];
      $path = array_slice($path,0,$pathlen-1);
      $pathlen = $pathlen-1;
      // populate the datums
      $parent = xmldb_getParentById($environment["Connection"],$catname["ChildOf"]);
      $data_options = array();
      if (!$parent)
        array_push($data_options,
          arpg_make_option_for_select("Content","Could not resolve path",false));
      array_push($data_options,
        arpg_make_option_for_select($environment["Message"],"Choose...",true));
      $names = xmldb_getAttributeOfAllChildren($environment["Connection"],
                  $parent,"datum","name");
      foreach ($names as $name) {
        $selected = false;
        if ($name["Value"] === $WhichDatum)
          $selected = true;
        array_push($data_options,
          arpg_make_option_for_select($environment["Message"]."@".$name["ID"],
            $name["Value"],$selected));
      }
      $nenv = array("Responder"=>"loader.php","Target"=>"'Path'",
                    "Source"=>"'Path'","Code"=>"'LoadDatum'",
                    "Message"=>"value");
      $datum_select = arpg_make_select_statement($data_options,$nenv);
    }

  // grab all of the possible candidates
  $uniquenames = array();
  foreach ($catnames as $catname) {
    $catpath = $catname["Value"];
    $catpathparts = explode("/",$catpath);
    if ($pathlen > sizeof($catpathparts))
      continue;
    $keep = true;
    for ($pdx = 0; $pdx < $pathlen; $pdx++)
      if ($path[$pdx] !== $catpathparts[$pdx])
        $keep = false;
    if ($keep) {
      $lastname = $catpathparts[$pathlen];
      if (!in_array($lastname,$uniquenames)) {
        $npath = implode("/",$path)."/".$catpathparts[$pathlen];
        $selected = false;
        if ($WhichCat === $lastname)
          $selected = true;
        array_push($options,arpg_make_option_for_select($npath,$lastname,$selected));
        array_push($uniquenames,$catpathparts[$pathlen]);
      }
    }
  }
  $nenv = array("Responder"=>"loader.php","Target"=>"'Path'","Source"=>"'Path'",
                "Code"=>"'LoadCategory'","Message"=>"value");
  $select = arpg_make_select_statement($options,$nenv);
  array_push($catparts,$select);
  if ($datum_select)
    array_push($catparts,$datum_select);
  return $catparts;
}

function build_text_area($PseudoXML,$TabOrder,$ExtraStyle) {
  $result = "<textarea tabindex='".$TabOrder."'"
              ."onFocus=\"focusStyle(this);\"
                onBlur=\"blurStyle(this);\"" 
              ." id='G".$PseudoXML["ID"]."'"
              ." style='background-image:url(pin-2x2.png);
                     background-repeat:repeat-x;
                     background-position:bottom;
                     text-align:justify;
                     ".$ExtraStyle."'
              onChange=\"ajaxFunction('loader.php',
                'Load','Load','UpdateValue@".$PseudoXML["ID"]."',value)\">";
  $result .= $PseudoXML["Value"];
  $result .= "</textarea>";
  return $result;
}

function __Build_modifyable_area($Node) {
  $result = "";
  foreach ($Node->childNodes as $child) {
    $result .= "<textarea>".$child->nodeValue."</textarea><br/>";
  }
  return $result;
}

function update_value_response($environment) {
  $at_parts = explode("@",$environment["Code"]);
  $Id = $at_parts[1];
  $message = $environment["Message"];
  $imsg = serialize_elements_for_sql($message,simple_edit_map());
  $field = xmldb_getElementById($environment["Connection"],$Id);
  $error = xmldb_setNodeValue($environment["Connection"],
                                $Id,$imsg);
  return arpg_build_responses(array("Log"),
    array("<em style='color:blue'>".$Id
        ."</em>&loz;<b>&laquo;</b><span style='color:green'>"
        .$field["Value"]."</span><b>&raquo;</b>&rArr;"
        ."<b>&laquo;</b><span style='color:red'>"
        .$environment["Message"]."</span><b>&raquo;</b>
        with error: <em>".$error["Error"]."</em>"));
}

function simple_display_map() {
  return array(
          "<Apathy/>"=>"<b>Apathy</b>",
          "<and/>"=>"&amp;",
          "<dollar/>"=>"$",
          "<percent/>"=>"%",
          "<rightarrow/>"=>"&rarr;",
          "<ldquo/>"=>"&ldquo;",
          "<rdquo/>"=>"&rdquo;",
          "<lsquo/>"=>"&lsquo;",
          "<rsquo/>"=>"&rsquo;",
          "<mdash/>"=>"&mdash;",
          "<ndash/>"=>"&ndash;",
          "<times/>"=>"&#215;",
          "<ouml/>"=>"&#246;",
          "<oslash/>"=>"&#248;",
          "<trademark/>"=>"&#8482;",
          "<Sum/>"=>"&#8721;");
}

function simple_edit_map() {
  return array(
          "<Apathy/>"=>"{Apathy}",
          "<and/>"=>"&",
          "<dollar/>"=>"$",
          "<percent/>"=>"%",
          "<rightarrow/>"=>"->",
          "<ldquo/>"=>"``",
          "<rdquo/>"=>"''",
          "<lsquo/>"=>"`",
          "<rsquo/>"=>"'",
          "<mdash/>"=>"---",
          "<ndash/>"=>"--",
          "<times/>"=>"{x}",
          "<ouml/>"=>"{\\\"o}",
          "<oslash/>"=>"{/o}",
          "<trademark/>"=>"{TM}",
          "<Sum/>"=>"{Sum}");
}

function inverse_map($Map) {
  $imap = array();
  foreach ($Map as $key => $value)
    $imap[$value] = $key;
  return $imap;
}

function serialize_elements_for_display($PseudoXML,$DisplayMap) {
  $result = $PseudoXML;
  if (is_array($PseudoXML))
    $result = $PseudoXML["Value"];
  foreach ($DisplayMap as $what => $toreplace)
    $result = str_replace($what,$toreplace,$result);
  return $result;
}

function serialize_elements_for_sql($PseudoXML,$DisplayMap) {
  $imap = inverse_map($DisplayMap);
  return serialize_elements_for_display($PseudoXML,$imap);
}

function build_modifyable_click_area($PseudoXML) {
  return "<p id='P".$PseudoXML["ID"]."
            style='border:1px solid blue;'
            onClick=\"ajaxFunction('loader.php',id,
              'DP".$PseudoXML["ID"]."',
              'InsertEditable@'+this.scrollWidth+':'+this.scrollHeight"
              ."+'@".$PseudoXML["ID"]."@DP".$PseudoXML["ID"]."',"
              ."this.innerHTML);\">"
            .serialize_elements_for_display($PseudoXML,simple_display_map())
          ."</p>";
}

function build_modifyable_raw_text($PseudoXML) {
  return "<div name='text' id='DP".$PseudoXML["ID"]."'>"
          .build_modifyable_click_area($PseudoXML)."</div>";
}

function build_modifyable_area($PseudoXMLs,$TabOrder,$ExtraStyle) {
  $result = "";
  $some_result = false;
  foreach ($PseudoXMLs as $ID => $PseudoXML)
    switch ($PseudoXML["Name"]) {
      case "text":
        $result .= build_modifyable_raw_text($PseudoXML);
        $some_result = true;
        break;
      default:
        $some_result = true;
        $result .= $PseudoXML["Name"]."@".$PseudoXML["ID"]."<br/>";
    }
  if (!$some_result)
    $result = "<em>No value.</em>";
  return $result;
}

function unload_editable_response($environment) {
  $rawtext_source = $environment["Message"];
  $rawtext = xmldb_getElementById($environment["Connection"],$rawtext_source);
  return arpg_build_response($environment["Target"],
    build_modifyable_click_area($rawtext));
}

function insert_editable_response($environment) {
  $at_code = explode("@",$environment["Code"]);
  $rawtext_source = $at_code[2];
  $parent_html_id = $at_code[3];
  $sizes = explode(":",$at_code[1]);
  $width = $sizes[0];
  $height = (int)$sizes[1]*3;
  $rawtext = xmldb_getElementById($environment["Connection"],$rawtext_source);
  $target = $environment["Target"];
  $payload = "<table class='NoStyle'><tr><td colspan='2'><textarea
                id='RTS".$rawtext_source."'
                onFocus=\"focusStyle(this);\"
                onBlur=\"blurStyle(this);\"
                style='height:".$height."px;width:".$width."px;'>"
                .serialize_elements_for_display($rawtext,
                    simple_edit_map()).
              "</textarea></td></tr><tr>
              <td><input type='button'
                value='Close without saving' class='ForceSave'
                onClick=\"ajaxFunction('loader.php',id,'"
                  .$parent_html_id."','UnloadEditable','"
                  .$rawtext_source."')\" /></td>
              <td align='right'><input type='button'
                value='Update database' class='ForceSave'
                onClick=\"ajaxFunction('loader.php',id,'"
                  .$parent_html_id."','UpdateValue@".$rawtext_source."',
                  document.getElementById('RTS".$rawtext_source."').value)\" />
              </td></tr></table>";
  return arpg_build_responses(array($target,"@Focus@RTS".$rawtext_source),
    array($payload,"PAIN"));
}

function build_datum_table($environment,$datum) {
  $attrs = xmldb_attributes($environment["Connection"],$datum);
  $children = xmldb_getChildNodes($environment["Connection"],$datum);
  $attributeset = xmldb_attributesOfSet($environment["Connection"],$children);
  $valueset = xmldb_getChildNodeValuesOfSet($environment["Connection"],$children);
  $title = null;
  $description = null;
  $entries = array();
  foreach ($children as $id => $child)
    if (array_key_exists("title",$attributeset[$id])
      and $attributeset[$id]["title"]["Value"] === "yes")
      $title = $id;
    else if (array_key_exists("description",$attributeset[$id])
      and $attributeset[$id]["description"]["Value"] === "yes")
      $description = $id;
    else
      $entries[$id] = $attributeset[$id]["name"]["Value"];
  $DIVS = "<table>
            <tr><td colspan='2' align='center'>
              <em>#".$datum["ID"]."</em></td></tr>
            <tr><td><div class='DatumLeftDiv'>";
  $DIVS .= "<table class='ModifyDatumTable' style='width:100%'>
                <thead><th><pre>Title</pre></th><th>"
                .build_modifyable_area($valueset[$title],1)."</th></thead>";
  foreach ($entries as $id => $entry )
    $DIVS .= "<tr><td align='right'
                style='width:10em;
                       font-variant:small-caps;
                       font-weight:bold;
                       font-style:italic;'>"
                  .$entry."&rsaquo;&rsaquo;&rsaquo;</td><td>"
                  .build_modifyable_area($valueset[$id],1)."</td></tr>";
  $DIVS .= "</table>";
  $DIVS .= "</div></td><td>";
  $DIVS .= "<div class='DatumRightDiv'>"
            .build_modifyable_area($valueset[$description],1)
            ."</div></td></tr></table>";
  return $DIVS;
}

function load_datum_response($environment) {
  $OMsg = $environment["Message"];
  $NMsg = explode("@",$OMsg);
  $environment["Message"] = $NMsg[0];
  $catpath = "";
  $datum_table = "<em>No data shown.</em>";
  if (sizeof($NMsg) > 1) {
    $nameattr = xmldb_getNodeById($environment["Connection"],$NMsg[1]);
    $datum = xmldb_getParent($environment["Connection"],$nameattr);
    $catpath = build_category_path($environment,$nameattr["Value"]);
    $datum_table = build_datum_table($environment,$datum);
  } else
    $catpath = build_category_path($environment,null);
  $targets = array(/*"Path",*/"Datum");
  $payloads = array(/*$catpath,*/$datum_table);
  return arpg_build_responses($targets,$payloads);
}

function build_category_path($environment,$WhichDatum) {
  $parts = array();

  $catpath = load_category_path($environment,$WhichDatum);
  foreach ($catpath as $catpart)
    array_push($parts,$catpart);

  $result = make_main_menu("RawData") . " " . make_arrow_path($parts);
  return $result;
}

function load_category_response($environment) {
  return arpg_build_responses(
    array("Path","Datum"),
    array(build_category_path($environment,null),"<em>Data not shown.</em>"));
}

function raw_data_response($environment) {
  // we're going to build a path
  // [main-menu] >> Raw Data >> [Content/]
  $environment["Message"] = "Content";
  return load_category_response($environment);
}

function initialize_system($environment) {
  $env["Connection"] = arpg_create_apathy("Apathy.xml");
  $payloads = array();
  array_push($payloads,make_main_menu("Choose"));
  array_push($payloads,"<em>No data shown.</em>");
  $targets = array();
  array_push($targets,"Path");
  array_push($targets,"Datum");
  return arpg_build_responses($targets,$payloads);
}

function respond() {
  $env = arpg_get_environment();
  $env["Connection"] = arpg_connect_to_apathy();
  if ("Initialize" === $env["Code"]) {
    return initialize_system($env);
  } else if ("NoResponse" === $env["Code"]) {
    return arpg_build_empty_response();
  } else if ("RebuildMainMenu" === $env["Code"]) {
    return initialize_system($env);
  } else if ("RawData" === $env["Code"]) {
    return raw_data_response($env);
  } else if ("LoadCategory" === $env["Code"]) {
    return load_category_response($env);
  } else if ("LoadDatum" === $env["Code"]) {
    return load_datum_response($env);
  } else if ("InsertEditable" === $env["Code"]) {
    return insert_editable_response($env);
  } else if ("UnloadEditable" === $env["Code"]) {
    return unload_editable_response($env);
  } else {
    $at_parts = explode("@",$env["Code"]);
    if ("UpdateValue" === $at_parts[0])
      return update_value_response($env);
    else if ("InsertEditable" === $at_parts[0])
    return insert_editable_response($env);
  }
  return arpg_build_response("Log",
    "<p><span style='color:red;'>Not a known code:</span>"
      .$env["Code"]." with <span style='color:red;'>"
      .$env["Target"]."&rArr;".$env["Source"]
      ."</span>&loz;<b>&laquo;</b><span style='color:green;'>"
      .$env["Message"]."</span><b>&raquo;</b> with ("
      .$env["Connection"].")</p>");
}

echo respond();

?>