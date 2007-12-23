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

function populate_structured_text($Node,$ParentId,$Indent) {
  $indent = "";
  $nindent = "";
  if (is_string($Indent)) {
    $indent = $Indent;
    $nindent = "&nbsp;&nbsp;" . $indent;
  }
  echo $indent.$ParentId."<br/>";
  $curP = $ParentId;
  foreach ($Node->childNodes as $Child) {
    populate_structured_text($Child,$ParentId+1,$nindent);
  }
}

function populate_database($Apathy) {
  $books = $Apathy->getElementsByTagName("book");
  for ($bad = 0; $bad < $books->length; $bad++) {
    $book = $books->item($bad);
    populate_structured_text($book,0,"");
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