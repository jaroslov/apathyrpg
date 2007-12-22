<?php
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

$categories = $Apathy->getElementsByTagName("category");
for ($cat = 0; $cat < $categories->length; $cdx++) {
  $category = $categories->item($cdx);
  $name = $category->getAttribute("name");
  echo "<p>Selecting ".$name."</p>";
  foreach ($category->childNodes as $child)
    if ("datum" === $child->tagName) {
      
      echo "<p>".$chname."</p>";
    }
}

?>