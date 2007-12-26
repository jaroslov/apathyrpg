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
  array_push($options,make_option_for_select("RebuildMainMenu","Choose...",$chsel));
  array_push($options,make_option_for_select("Book","Book",$bksel));
  array_push($options,make_option_for_select("RawData","Raw Data",$rdsel));
  $env = array("Responder"=>"loader.php",
               "Target"=>"'Path'",
               "Source"=>"'Path'",
               "Code"=>"value",
               "Message"=>"''");
  return make_select_statement($options,$env);
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
  array_push($options,make_option_for_select("NoResponse","Choose...",true));
  if (sizeof($path) > 1) {
    $npath = array();
    for ($pdx = 0; $pdx < $pathlen-1; $pdx++)
      array_push($npath,$path[$pdx]);
    array_push($options,
      make_option_for_select(implode("/",$npath),"&laquo; ".$path[$pathlen-2],false));
  } else {
    array_push($options,
      make_option_for_select("Content","&laquo; Content",false));
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
          make_option_for_select("Content","Could not resolve path",false));
      array_push($data_options,
        make_option_for_select($environment["Message"],"Choose...",true));
      $names = xmldb_getAttributeOfAllChildren($environment["Connection"],
                  $parent,"datum","name");
      foreach ($names as $name) {
        $selected = false;
        if ($name["Value"] === $WhichDatum)
          $selected = true;
        array_push($data_options,
          make_option_for_select($environment["Message"]."@".$name["ID"],
            $name["Value"],$selected));
      }
      $nenv = array("Responder"=>"loader.php","Target"=>"'Path'",
                    "Source"=>"'Path'","Code"=>"'LoadDatum'",
                    "Message"=>"value");
      $datum_select = make_select_statement($data_options,$nenv);
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
        array_push($options,make_option_for_select($npath,$lastname,$selected));
        array_push($uniquenames,$catpathparts[$pathlen]);
      }
    }
  }
  $nenv = array("Responder"=>"loader.php","Target"=>"'Path'","Source"=>"'Path'",
                "Code"=>"'LoadCategory'","Message"=>"value");
  $select = make_select_statement($options,$nenv);
  array_push($catparts,$select);
  if ($datum_select)
    array_push($catparts,$datum_select);
  return $catparts;
}

function encapsulate_free_text($PseudoXML) {
  // The most common offender is the FIELD element;
  // Most of them contain free text.

  // comment out the next line if your fields are well formed.
  return __Free_text($PseudoXML);
  $LDom = new DOMDocument();
  $LDom->loadXML("<apathy:pseudo-xml-root>"
            .$PseudoXML."</apathy:pseudo-xml-root>");
  $field = $LDom->getElementsByTagName("field")->item(0);
  foreach ($field->childNodes as $child)
    if ($child->nodeType == XML_TEXT_NODE)
      $result .= "<text>".$child->nodeValue."</text>";
    else if ($child->nodeType == XML_ELEMENT_NODE)
      $result .= $LDom->saveXML($child);
  if (strlen($result) === 0)
    $result .= "<text></text>";
  return "<field>".$result."</field>";
}

function __Free_text($PseudoXML) {
  $LDom = new DOMDocument();
  $LDom->loadXML("<apathy:pseudo-xml-root>"
            .$PseudoXML."</apathy:pseudo-xml-root>");
  $field = $LDom->getElementsByTagName("field")->item(0);
  foreach ($field->childNodes as $child)
    if ($child->nodeType == XML_TEXT_NODE)
      $result .= $child->nodeValue;
    else if ($child->nodeType == XML_ELEMENT_NODE)
      $result .= $LDom->saveXML($child);
  return "<field>\n".$result."\n</field>";
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
  $field = xmldb_getElementById($environment["Connection"],$Id);
  $error = xmldb_setNodeValue($environment["Connection"],
                                $Id,$environment["Message"]);
  return build_responses(array("Log"),
    array("<em style='color:blue'>".$Id
        ."</em>&loz;<b>&laquo;</b><span style='color:green'>"
        .$field["Value"]."</span><b>&raquo;</b>&rArr;"
        ."<b>&laquo;</b><span style='color:red'>"
        .$environment["Message"]."</span><b>&raquo;</b>
        with error: <em>".$error["Error"]."</em>"));
}

function build_modifyable_area($PseudoXMLs,$TabOrder,$ExtraStyle) {
  $result = "";
  foreach ($PseudoXMLs as $ID => $PseudoXML)
    if ("text" === $PseudoXML["Name"]) {
      $result .= "<div name='text' id='DP".$PseudoXML["ID"]."'>
                  <p id='P".$PseudoXML["ID"]."
                    style='border:1px solid blue;'
                    onClick=\"ajaxFunction('loader.php',id,
                      'DP".$PseudoXML["ID"]."',
                      'InsertEditable@'+this.scrollWidth+':'+this.scrollHeight,
                      this.innerHTML);\">"
                    .$PseudoXML["Value"]
                  ."</p></div>";
    } else
      $result .= $PseudoXML["Name"]."@".$PseudoXML["ID"]."<br/>";
  return $result;
}

function insert_editable_response($environment) {
  $at_code = explode("@",$environment["Code"]);
  $sizes = explode(":",$at_code[1]);
  $width = $sizes[0];
  $height = (int)$sizes[1]*1.5;
  $target = $environment["Target"];
  $payload = "<textarea 
                onClick=\"\"
                style='height:".$height."px;width:".$width."px;'>
                ".$environment["Message"]."
              </textarea>";
  return build_response($target,$payload);
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
    $DIVS .= "<tr><td align='right'>
                <pre>".$entry.":&rsaquo;</pre></td><td>"
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
  $targets = array("Path","Datum");
  $payloads = array($catpath,$datum_table);
  return build_responses($targets,$payloads);
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
  return build_responses(
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
  $env["Connection"] = create_apathy("Apathy.xml");
  $payloads = array();
  array_push($payloads,make_main_menu("Choose"));
  array_push($payloads,"<em>No data shown.</em>");
  $targets = array();
  array_push($targets,"Path");
  array_push($targets,"Datum");
  return build_responses($targets,$payloads);
}

function respond() {
  $env = get_environment();
  $env["Connection"] = connect_to_apathy();
  if ("Initialize" === $env["Code"]) {
    return initialize_system($env);
  } else if ("NoResponse" === $env["Code"]) {
    return build_empty_response();
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
  } else {
    $at_parts = explode("@",$env["Code"]);
    if ("UpdateValue" === $at_parts[0])
      return update_value_response($env);
    else if ("InsertEditable" === $at_parts[0])
    return insert_editable_response($env);
  }
  return build_response("Log",
    "<p><span style='color:red;'>Not a known code:</span>"
      .$env["Code"]." with <span style='color:red;'>"
      .$env["Target"]."&rArr;".$env["Source"]
      ."</span>&loz;<b>&laquo;</b><span style='color:green;'>"
      .$env["Message"]."</span><b>&raquo;</b> with ("
      .$env["Connection"].")</p>");
}

echo respond();

?>