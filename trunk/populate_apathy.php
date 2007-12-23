<?php

include 'apathy_xml.php';

// Load the apathy xml-database
$ApathyName = "Apathy.xml";
$ApathyXml = simplexml_load_file($ApathyName);
$ApathyDom = dom_import_simplexml($ApathyXml);
$Apathy = $ApathyDom->ownerDocument;

// Connecting, selecting database
$link = mysql_connect('localhost', 'thechao', 'ha1l3r1S')
    or die('Could not connect: ' . mysql_error());

echo '<p>Connected successfully.</p>';

mysql_select_db('Apathy') or die('Could not select database');

echo '<p>Connected to Apathy database.</p>';

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
              `DescP`,
              `Description`
            ) VALUES (
              NULL,
              '".$BelongsTo."',
              '".$Title."',
              '".$Table."',
              '".$Desc."',
              '".$Description."');";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function insert_field_title_element($BelongsTo,$Description) {
  return insert_field_data_element($BelongsTo,1,0,0,$Description);
}

function insert_field_table_element($BelongsTo,$Description) {
  return insert_field_data_element($BelongsTo,0,1,0,$Description);
}

function insert_field_desc_element($BelongsTo,$Description) {
  return insert_field_data_element($BelongsTo,0,0,1,$Description);
}

function insert_field_anon_element($BelongsTo,$Description) {
  return insert_field_data_element($BelongsTo,0,0,0,$Description);
}

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
            insert_field_title_element($datum_id,$strxml);
          else if (false !== $field_p->hasAttribute("table")
            and "yes" === $field_p->getAttribute("table"))
            insert_field_table_element($datum_id,$strxml);
          else if (false !== $field_p->hasAttribute("description")
            and "yes" === $field_p->getAttribute("description"))
            insert_field_desc_element($datum_id,$strxml);
          else
            insert_field_anon_element($datum_id,$strxml);
        }
    }
}

?>