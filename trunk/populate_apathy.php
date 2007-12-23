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

function populate_structured_text($Node,$ParentId,$saverawtext,$Indent) {
  $indent = "";
  $nindent = "";
  if (is_string($Indent)) {
    $indent = $Indent;
    $nindent = "&nbsp;&nbsp;" . $indent;
  }
  $belongsto = $ParentId;
  $tagname = $Node->tagName;
  $extra = "";
  $rawtext = null;
  $recurse = true;
  $default_print = false;
  switch ($tagname) {
    // structure elements
    case "book": $saverawtext = false; break;
    case "section": $saverawtext = false;
      $extra = $Node->getAttribute("kind"); break;
    // structured text elements
    case "title": $saverawtext = true; break;
    case "text": $saverawtext = true; break;
    case "itemized-list": $saverawtext = false; break;
    case "description-list": $saverawtext = false; break;
    case "numbered-list": $saverawtext = false; break;
    case "description": $saverawtext = true; break;
    case "item": $saverawtext = false; break;
    case "define": $saverawtext = true; break;
    case "footnote": $saverawtext = false; break;
    case "figure": $saverawtext = false; break;
    case "table": $saverawtext = false; break;
    case "head": $saverawtext = false; break;
    case "row": $saverawtext = false; break;
    case "cell": $saverawtext = true;
      $extra = $Node->getAttribute("colfmt"); break;
    case "caption": $saverawtext = true; break;
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
    // unstructured elements
    case "reference": $saverawtext = false;
      $recurse=false;
      $extra=$Node->getAttribute("hrid"); break;
    case "summarize": $saverawtext = false;
      $recurse=false;
      $extra=$Node->getAttribute("hrid"); break;
    case "Apathy": $saverawtext = false; $recurse=false; break;
    case "C": $saverawtext = false; $recurse=false; break;
    case "notappl": $saverawtext = false; $recurse=false; break;
    case "mul": $tagname = "times";
      $saverawtext = false;
      $recurse=false; break;
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
  if ($Node->nodeType == XML_TEXT_NODE) {
    $spaces = array(" ","\t","\n","\r");
    $value = str_replace(" ","",$Node->nodeValue);
    $value = str_replace("\t","t",$value);
    $value = str_replace("\r","r",$value);
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
  if (true)//$recurse)
    foreach ($Node->childNodes as $Child) {
      populate_structured_text($Child,$ParentId+1,$saverawtext,$nindent);
    }
}

function populate_database($Apathy) {
  $books = $Apathy->getElementsByTagName("book");
  for ($bad = 0; $bad < $books->length; $bad++) {
    $book = $books->item($bad);
    populate_structured_text($book,0,false,"");
    echo "<p>".$book->getAttribute("xml:id")."</p>";
  }
  return "<b>Books Finished</b><br/>";
  $categories = $Apathy->getElementsByTagName("category");
  for ($cat = 0; $cat < $categories->length; $cdx++) {
    $category = $categories->item($cdx);
    $path = $category->getAttribute("name");
    // (1) insert the category
    $cat_id = insert_raw_category_element($path);
    echo "<p><em>".$cat_id."</em> ".$path."</p>";
    foreach ($category->childNodes as $datum_p)
      if ("datum" === $datum_p->tagName) {
        $datum_name = $datum_p->getAttribute("name");
        // (2) insert the datum
        $datum_id = insert_raw_datum_element($cat_id,$datum_name);
        echo "<p>&nbsp;&nbsp;<em>".$datum_id."</em> ".$datum_name."</p>";
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

echo populate_database($Apathy);

?>