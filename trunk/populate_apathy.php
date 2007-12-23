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

function insert_raw_category_element($Path,$Name) {
  $query = "INSERT INTO `Apathy`.`RawCategories` ("
            ."`EntryId`, `Category`, `Name`)"
            ."VALUES ( NULL, '".$Path."', '".$Name."');";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function insert_field_data_element($Title,$Table,$Desc,$Belongs,$Description) {
  $query = "INSERT INTO `Apathy`.`FieldData` ("
        ."`FieldId`,`TitleP`,`TableP`,`DescP`,`BelongsTo`,`Description`)"
        ."VALUES (NULL, '"
        .$Title."','".$Table."','".$Desc."',"
        ."'".$Belongs."','".$Description."');";
  $resource = mysql_query($query);
  return mysql_insert_id();
}

function insert_field_title_element($Belongs,$Description) {
  return insert_field_data_element(1,0,0,$Belong,$Description);
}

function insert_field_table_element($Belongs,$Description) {
  return insert_field_data_element(0,1,0,$Belong,$Description);
}

function insert_field_anon_element($Belongs,$Description) {
  return insert_field_data_element(0,0,0,$Belong,$Description);
}

function insert_field_desc_element($Belongs,$Description) {
  return insert_field_data_element(0,0,1,$Belong,$Description);
}

$categories = $Apathy->getElementsByTagName("category");
for ($cat = 0; $cat < $categories->length; $cdx++) {
  $category = $categories->item($cdx);
  $path = $category->getAttribute("name");
  echo "<p>Selecting ".$path."</p>";
  foreach ($category->childNodes as $child)
    if ("datum" === $child->tagName) {
      $title = get_title_of_datum($child);
      $tables = get_table_of_datum($child);
      $description = get_description_of_datum($child);
      echo "<p>".$title." ".sizeof($tables)." ".$description."</p>";
    }
}

?>