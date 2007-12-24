<?php

include 'apathy_xml.php';

function create_connection() {
  // Connecting, selecting database
  $link = mysql_connect('localhost', 'thechao', 'ha1l3r1S')
      or die('Could not connect: ' . mysql_error());  
  mysql_select_db('Apathy') or die('Could not select database');
  return $link;
}

function close_connection($connection) {
  mysql_close($connection);
}

function insert_raw_category_element($Path,$connection) {
  $query = "INSERT INTO `Apathy`.`RawCategories` ("
            ."`CategoryId`, `Path`)"
            ."VALUES ( NULL, '".$Path."');";
  $resource = mysql_query($query,$connection);
  return mysql_insert_id();
}

function insert_raw_datum_element($BelongsTo,$Name,$connection) {
  $query = "INSERT INTO `Apathy`.`RawDatums` (
              `DatumId` ,
              `BelongsTo` ,
              `Name`
            )
            VALUES (
              NULL , '".$BelongsTo."', '".$Name."'
            );";
  $resource = mysql_query($query,$connection);
  return mysql_insert_id();
}

function insert_field_data_element($BelongsTo,$Name,$Title,$Table,$Desc,$connection) {
  $query = "INSERT INTO `Apathy`.`RawDataFields` (
              `FieldId`,
              `BelongsTo`,
              `Name`,
              `TitleP`,
              `TableP`,
              `DescP`
            ) VALUES (
              NULL,
              '".$BelongsTo."',
              '".$Name."',
              '".$Title."',
              '".$Table."',
              '".$Desc."');";
  $resource = mysql_query($query,$connection);
  return mysql_insert_id();
}

function insert_field_title_element($BelongsTo,$Name,$connection) {
  return insert_field_data_element($BelongsTo,$Name,'yes','no','no',$connection);
}

function insert_field_table_element($BelongsTo,$Name,$connection) {
  return insert_field_data_element($BelongsTo,$Name,'no','yes','no',$connection);
}

function insert_field_desc_element($BelongsTo,$Name,$connection) {
  return insert_field_data_element($BelongsTo,$Name,'no','no','yes',$connection);
}

function insert_field_anon_element($BelongsTo,$Name,$connection) {
  return insert_field_data_element($BelongsTo,$Name,'no','no','no',$connection);
}

function insert_structured_element($BelongsTo,$OwnerKind,$Order,$TagName,
                                   $ExtraName,$ExtraValue,
                                   $RawTextP,$connection) {
  $query = "INSERT INTO `Apathy`.`StructuredText` (
              `StructuredId` ,
              `BelongsTo` ,
              `OwnerKind` ,
              `Order` ,
              `TagName` ,
              `ExtraName` ,
              `ExtraValue` ,
              `RawTextP`
            ) VALUES (
              NULL , '".$BelongsTo."', '".$OwnerKind."', '"
              .$Order."', '".$TagName."', '"
              .$ExtraName."', '".$ExtraValue."', '"
              .$RawTextP."'
            );";
  $resource = mysql_query($query,$connection);
  return mysql_insert_id();
}

function insert_raw_text_chunk($BelongsTo,$Text,$connection) {
  $query = "INSERT INTO `Apathy`.`RawText` (
              `TextId` ,
              `BelongsTo` ,
              `Value`
            ) VALUES (
              NULL , '".$BelongsTo."', '".$Text."'
            );";
  $resource = mysql_query($query,$connection);
  return mysql_insert_id();
}

function mark_as_populated ($connection) {
  $query = "INSERT INTO `Apathy`.`Populated` (
              `Populated`
            ) VALUES (
              NULL
            );";
  $resource = mysql_query($query,$connection);
  return mysql_insert_id();  
}

function populated_p($connection) {
  $query = "SELECT * FROM `Populated`";
  $resource = mysql_query($query,$connection);
  $populated = false;
  while ($row = mysql_fetch_array($resource))
    $populated = true;
  return $populated;
}

function populate_structured_text($Node,$Whom,$ParentId,$Order,$saverawtext,$Indent,$connection) {
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
    case "datum": $saverawtext = false; break;
    case "default": $saverawtext = false; break;
    // structured text elements
    case "title": $saverawtext = true; break;
    case "text": $saverawtext = true; break;
    case "field": $saverawtext = true;
      $extraname = "colfmt";
      $extravalue = $Node->getAttribute($extraname);
      break;
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
    if ($saverawtext) {
      $sxml = simplexml_import_dom($Node);
      $node_id = insert_structured_element($ParentId,$Whom,$Order,$tagname,"","","yes",$connection);
      insert_raw_text_chunk($node_id,$sxml->asXML(),$connection);
      return true;
    } else {
      $node_id = insert_structured_element($ParentId,$Whom,$Order,$tagname,$extraname,$extravalue,"no",$connection);
      if ($recurse) {
        $order = 0;
        foreach ($Node->childNodes as $Child) {
          $increment = populate_structured_text($Child,$Whom,$node_id,$order,$saverawtext,$nindent,$connection);
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

function populate_database($Apathy,$connection) {
  if (!populated_p($connection))
    DO_NOT_USE_force_populate_database($Apathy,$connection);
  return populated_p($connection);
}

function DO_NOT_USE_force_populate_database($Apathy,$connection) {
  mark_as_populated($connection);
  populate_books($Apathy,$connection);
  populate_categories($Apathy,$connection);
}

function populate_books($Apathy,$connection) {
  $books = $Apathy->getElementsByTagName("book");
  for ($bad = 0; $bad < $books->length; $bad++) {
    $book = $books->item($bad);
    populate_structured_text($book,"books",0,$bad,false,"",$connection);
  }
}

function populate_categories($Apathy,$connection) {
  $categories = $Apathy->getElementsByTagName("category");
  for ($cat = 0; $cat < $categories->length; $cdx++) {
    $category = $categories->item($cdx);
    $path = $category->getAttribute("name");
    // (1) insert the category
    $cat_id = insert_raw_category_element($path,$connection);
    foreach ($category->childNodes as $datum_p)
      if ("datum" === $datum_p->tagName) {
        $datum_name = $datum_p->getAttribute("name");
        // (2) insert the datum
        $datum_id = insert_raw_datum_element($cat_id,$datum_name,$connection);
        foreach ($datum_p->childNodes as $field_p)
          if ("field" === $field_p->tagName) {
            // (3) insert the fields
            $name = $field_p->getAttribute("name");
            $strxml = translate_child_text($field_p);
            $field_id = null;
            if (false !== $field_p->hasAttribute("title")
              and "yes" === $field_p->getAttribute("title"))
              $field_id = insert_field_title_element($datum_id,$name,$connection);
            else if (false !== $field_p->hasAttribute("table")
              and "yes" === $field_p->getAttribute("table"))
              $field_id = insert_field_table_element($datum_id,$name,$connection);
            else if (false !== $field_p->hasAttribute("description")
              and "yes" === $field_p->getAttribute("description"))
              $field_id = insert_field_desc_element($datum_id,$name,$connection);
            else
              $field_id = insert_field_anon_element($datum_id,$name,$connection);
            $sxml = simplexml_import_dom($field_p);
            populate_structured_text($field_p,"categories",$field_id,0,false,"",$connection);
          }
      }
  }
}

function empty_database($connection) {
  $query = "TRUNCATE TABLE `Populated`";
  mysql_query($query);
  $query = "TRUNCATE TABLE `RawCategories`";
  mysql_query($query);
  $query = "TRUNCATE TABLE `RawDataFields`";
  mysql_query($query);
  $query = "TRUNCATE TABLE `RawDatums`";
  mysql_query($query);
  $query = "TRUNCATE TABLE `RawText`";
  mysql_query($query);
  $query = "TRUNCATE TABLE `StructuredText`";
  mysql_query($query);
}

function extract_structured_text($owner_id,$ownerkind,$connection) {
  $query = "SELECT * FROM `StructuredText`
              WHERE `BelongsTo` = ".$owner_id
            ."AND `OwnerKind` = CONVERT(_utf8 '".$ownerkind."'
              USING latin1)";
  $stext_resource = mysql_query($query);
  $result = array();
  while ($stext_resource = mysql_fetch_array($stext_resource)) {
    $lre = array();
    $lre["Id"] = $stext_resource["StructuredId"];
    $lre["Order"] = (int)$stext_resource["Order"];
    $lre["TagName"] = $stext_resource["TagName"];
    $extran = explode(",",$stext_resource["ExtraName"]);
    $extrav = explode(",",$stext_resource["ExtraValue"]);
    for ($edx = 0; $edx < sizeof($extran); $edx++)
      $lre[$extran[$edx]] = $extrav[$edx];
    $lre["RawText?"] = $stext_resource["RawTextP"];
    array_push($result,$lre);
  }
  return $result;
}

function extract_categories($connection) {
  $query = "SELECT * FROM `RawCategories`";
  $cat_resource = mysql_query($query);
  while ($cat_record = mysql_fetch_array($cat_resource)) {
    $category_id = $cat_record["CategoryId"];
    $path = $cat_record["Path"];
    echo "<p><b>".$category_id."</b> <em>".$path."</em></p>";
    $query = "SELECT * FROM `RawDatums` WHERE `BelongsTo` = ".$category_id;
    $datum_resource = mysql_query($query);
    while ($datum_record = mysql_fetch_array($datum_resource)) {
      $datum_id = $datum_record["DatumId"];
      $datum_name = $datum_record["Name"];
      echo "<p>&nbsp;&nbsp;<b>".$datum_id."</b> <em>".$datum_name."</em></p>";
      $query = "SELECt * FROM `RawDataFields` WHERE `BelongsTo` = ".$datum_id;
      $field_resource = mysql_query($query);
      while ($field_record = mysql_fetch_array($field_resource)) {
        $field_id = $field_record["FieldId"];
        $name = $field_record["Name"];
        $title_p = $field_record["TitleP"];
        $table_p = $field_record["TableP"];
        $desc_p = $field_record["DescP"];
        echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;".$name." ".$title_p."/".$table_p."/".$desc_p."</p>";
        extract_structured_text($field_id,"categories",$connection);
      }
    }
  }
}

function populate_or_empty ($populate) {
  // Load the apathy xml-database
  $ApathyName = "Apathy.xml";
  $ApathyDom = get_apathy_dom($ApathyName);
  $Apathy = get_apathy_xml($ApathyDom);

  $connection = create_connection();
  if (!$populate)
    empty_database($connection);
  else
    populate_database($Apathy,$connection);
  if (populated_p($connection))
    echo "<p>Database populated.</p>";
  else
    echo "<p>Database unpopulated.</p>";
  close_connection($connection);
}

function extract_apathy_as_xml() {
  $connection = create_connection();
  extract_categories($connection);
  close_connection($connection);
}

populate_or_empty(true);
extract_apathy_as_xml();

?>