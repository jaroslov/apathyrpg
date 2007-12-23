<?php

include 'apathy_xml.php';

// Load the apathy xml-database
$ApathyName = "Apathy.xml";
$ApathyDom = get_apathy_dom($ApathyName);
$Apathy = get_apathy_xml($ApathyDom);

// Connecting, selecting database
$link = mysql_connect('localhost', 'thechao', 'ha1l3r1S')
    or die('Could not connect: ' . mysql_error());

mysql_select_db('Apathy') or die('Could not select database');

function insert_raw_category_element($Path) {
  $query = "INSERT INTO `Apathy`.`RawCategories` ("
            ."`CategoryId`, `Name`)"
            ."VALUES ( NULL, '".$Path."');";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function insert_raw_datum_element($BelongsTo,$Name) {
  $query = "INSERT INTO `Apathy`.`RawDatums` (
              `DatumId` ,
              `BelongsTo` ,
              `Name`
            )
            VALUES (
              NULL , '".$BelongsTo."', '".$Name."'
            );";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function insert_field_data_element($BelongsTo,$Title,$Table,$Desc,$Description) {
  $query = "INSERT INTO `Apathy`.`RawDataFields` (
              `FieldId`,
              `BelongsTo`,
              `TitleP`,
              `TableP`,
              `DescP`
            ) VALUES (
              NULL,
              '".$BelongsTo."',
              '".$Title."',
              '".$Table."',
              '".$Desc."');";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function insert_field_title_element($BelongsTo) {
  return insert_field_data_element($BelongsTo,1,0,0);
}

function insert_field_table_element($BelongsTo) {
  return insert_field_data_element($BelongsTo,0,1,0);
}

function insert_field_desc_element($BelongsTo) {
  return insert_field_data_element($BelongsTo,0,0,1);
}

function insert_field_anon_element($BelongsTo) {
  return insert_field_data_element($BelongsTo,0,0,0);
}

function insert_structured_element($BelongsTo,$Order,$TagName,$ExtraName,$ExtraValue) {
  $query = "INSERT INTO `Apathy`.`StructuredText` (
              `StructuredId` ,
              `BelongsTo` ,
              `Order` ,
              `TagName` ,
              `ExtraName` ,
              `ExtraValue`
            )
            VALUES (
              NULL , '".$BelongsTo."', '".$Order."', '"
              .$TagName."', '"
              .$ExtraName."', '".$ExtraValue."'
            );";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function insert_raw_text_chunk($BelongsTo,$Text) {
  $query = "INSERT INTO `Apathy`.`RawText` (
              `TextId` ,
              `BelongsTo` ,
              `Value`
            )
            VALUES (
              NULL , '".$BelongsTo."', '".$Text."'
            );";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function mark_as_populated () {
  $query = "INSERT INTO `Apathy`.`Populated` (
              `Populated`
            ) VALUES (
              NULL
            );";
  $resource = mysql_query($query);
  return mysql_insert_id();  
}

function populated_p() {
  $query = "SELECT *
            FROM `Populated`
            LIMIT 0 , 30 ";
  $resource = mysql_query($query);
  $populated = false;
  while ($row = mysql_fetch_array($resource))
    $populated = true;
  mysql_free_result($result);
  return $populated;
}

function populate_structured_text($Node,$ParentId,$Order,$saverawtext,$Indent) {
  $indent = "";
  $nindent = "";
  if (is_string($Indent)) {
    $indent = $Indent;
    $nindent = "&nbsp;&nbsp;" . $indent;
  }
  $belongsto = $ParentId;
  $tagname = $Node->tagName;
  $extraname = "";
  $extravalue = "";
  $rawtext = null;
  $recurse = true;
  $default_print = false;
  $inlinde = false;
  switch ($tagname) {
    // structure elements
    case "book": $saverawtext = false; break;
    case "section": $saverawtext = false;
      $extraname = "kind";
      $extravalue = $Node->getAttribute($extraname); break;
    // structured text elements
    case "title": $saverawtext = true; break;
    case "text": $saverawtext = true; break;
    case "itemized-list": $saverawtext = false; break;
    case "description-list": $saverawtext = false; break;
    case "numbered-list": $saverawtext = false; break;
    case "description": $saverawtext = false; break;
    case "item": $saverawtext = false; break;
    case "define": $saverawtext = true; break;
    case "footnote": $saverawtext = false; break;
    case "figure": $saverawtext = false; break;
    case "table": $saverawtext = false; break;
    case "head": $saverawtext = false; break;
    case "row": $saverawtext = false; break;
    case "cell": $saverawtext = false;
      $colfmt = "";
      $border = "";
      $span = "";
      if ($Node->hasAttribute("colfmt"))
        $colfmt = $Node->getAttribute("colfmt");
      if ($Node->hasAttribute("border"))
        $border = $Node->getAttribute("border");
      if ($Node->hasAttribute("span"))
        $span = $Node->getAttribute("span");
      $extraname = "colfmt,border,span";
      $extravalue = implode(",",array($colfmt,$border,$span));
      break;
    case "caption": $saverawtext = false; break;
    case "note": $saverawtext = false; break;
    case "equation": $saverawtext = false; break;
    case "math": $saverawtext = false; break;
    case "mrow": $saverawtext = false; break;
    case "mfrac": $saverawtext = false; break;
    case "munderover": $saverawtext = false; break;
    case "mstyle": $saverawtext = false; break;
    case "msup": $saverawtext = false; break;
    case "mn": $saverawtext = true; break;
    case "mo": $saverawtext = true; break;
    case "mi": $saverawtext = true; break;
    case "example": $saverawtext = false; break;
    case "roll": $saverawtext = false; break;
    case "num": $saverawtext = true; break;
    case "face": $saverawtext = true; break;
    case "bns": $saverawtext = true; break;
    case "bOff": $saverawtext = true; break;
    case "rOff": $saverawtext = true; break;
    case "raw": $saverawtext = true; break;
    case "kind": $saverawtext = true; break;
    case "mul": $saverawtext = true; break;
    // unstructured elements
    case "reference": $saverawtext = false;
      $recurse = false;
      $extraname = "hrid";
      $extravalue = $Node->getAttribute($extraname); break;
    case "summarize": $saverawtext = false;
      $recurse = false;
      $extraname = "hrid";
      $extravalue = $Node->getAttribute($extraname); break;
    case "Apathy": $saverawtext = false; $recurse=false; break;
    case "C": $saverawtext = false; $recurse=false; break;
    case "notappl": $saverawtext = false; $recurse=false; break;
    case "and": $saverawtext = false; $recurse=false; break;
    case "percent": $saverawtext = false; $recurse=false; break;
    case "dollar": $saverawtext = false; $recurse=false; break;
    case "rsquo": $saverawtext = false; $recurse=false; break;
    case "lsquo": $saverawtext = false; $recurse=false; break;
    case "rdquo": $saverawtext = false; $recurse=false; break;
    case "ldquo": $saverawtext = false; $recurse=false; break;
    case "mdash": $saverawtext = false; $recurse=false; break;
    case "ndash": $saverawtext = false; $recurse=false; break;
    case "times": $saverawtext = false; $recurse=false; break;
    case "rightarrow": $saverawtext = false; $recurse=false; break;
    case "ouml": $saverawtext = false; $recurse=false; break;
    case "oslash": $saverawtext = false; $recurse=false; break;
    case "trademark": $saverawtext = false; $recurse=false; break;
    case "plusminus": $saverawtext = false; $recurse=false; break;
    case "Sum": $saverawtext = false; $recurse=false; break;
    default:
      if ($Node->nodeType != XML_TEXT_NODE) {
        $default_print = true;
        echo $indent."<span style='color:red;'>".$ParentId
          ."(".$saverawtext."):"
          .$Node->tagName."</span><br/>";
      }
  }
  $node_id = null;
  if ($Node->nodeType == XML_ELEMENT_NODE) {
    if ($tagname === "text") {
      $sxml = simplexml_import_dom($Node);
      $node_id = insert_structured_element($ParentId,$Order,"text","","");
      insert_raw_text_chunk($node_id,$sxml->asXML());
      return true;
    } else {
      $node_id = insert_structured_element($ParentId,$Order,$tagname,$extraname,$extravalue);
      if ($recurse) {
        $order = 0;
        foreach ($Node->childNodes as $Child) {
          $increment = populate_structured_text($Child,$node_id,$order,$saverawtext,$nindent);
          if ($increment)
            $order++;
        }
      }
      return true;
    }
  }
  return false;
  /*if ($Node->nodeType == XML_TEXT_NODE) {
    $spaces = array(" ","\t","\n","\r");
    $value = str_replace(" ","",$Node->nodeValue);
    $value = str_replace("\t","",$value);
    $value = str_replace("\r","",$value);
    $value = str_replace("\n","",$value);
    if (!$saverawtext) {
      if (0 < strlen($value)) {
        echo $indent."[Redact!]<span style='color:blue'>&laquo;"
          .$Node->nodeValue."&raquo;</span><br/>"; 
      }
    } else {
      echo $indent."<span style='font-weight:bold'>&laquo;"
        .$Node->nodeValue."&raquo;</span><br/>"; 
    }
  } else {
    echo $indent.$ParentId."(".$saverawtext."): ".$tagname."<br/>";
  }
  foreach ($Node->childNodes as $Child)
    populate_structured_text($Child,$node_id,$saverawtext,$nindent);*/
}

function populate_database($Apathy) {
  if (!populated_p())
    force_populate_database($Apathy);
  return populated_p();
}

function force_populate_database($Apathy) {
  mark_as_populated();
  $books = $Apathy->getElementsByTagName("book");
  for ($bad = 0; $bad < $books->length; $bad++) {
    $book = $books->item($bad);
    populate_structured_text($book,0,$bad,false,"");
  }
  $categories = $Apathy->getElementsByTagName("category");
  for ($cat = 0; $cat < $categories->length; $cdx++) {
    $category = $categories->item($cdx);
    $path = $category->getAttribute("name");
    // (1) insert the category
    $cat_id = insert_raw_category_element($path);
    foreach ($category->childNodes as $datum_p)
      if ("datum" === $datum_p->tagName) {
        $datum_name = $datum_p->getAttribute("name");
        // (2) insert the datum
        $datum_id = insert_raw_datum_element($cat_id,$datum_name);
        foreach ($datum_p->childNodes as $field_p)
          if ("field" === $field_p->tagName) {
            // (3) insert the fields
            $strxml = translate_child_text($field_p);
            if (false !== $field_p->hasAttribute("title")
              and "yes" === $field_p->getAttribute("title"))
              insert_field_title_element($datum_id);
            else if (false !== $field_p->hasAttribute("table")
              and "yes" === $field_p->getAttribute("table"))
              insert_field_table_element($datum_id);
            else if (false !== $field_p->hasAttribute("description")
              and "yes" === $field_p->getAttribute("description"))
              insert_field_desc_element($datum_id);
            else
              insert_field_anon_element($datum_id);
          }
      }
  }
}

if (populate_database($Apathy))
  echo "Populated.";
else
  echo "Not Populated.";

?>