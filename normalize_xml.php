<?php

// Normalize XML
// convert an XML document into a database
include 'apathy_xml.php';

function create_connection($Database) {
  // Connecting, selecting database
  $link = mysql_connect('localhost', 'thechao', 'ha1l3r1S')
      or die('Could not connect: ' . mysql_error());  
  mysql_select_db($Database) or die('Could not select database');
  return $link;
}

function insert_structural($Table,$Connection,$ParentId,$Kind,$Name,$Value) {
  $query = "INSERT INTO `".$Table."`.`Structural` (
            `ID`, `ChildOf`, `Kind`, `Name`, `Value`
            ) VALUES (
              NULL , '".$ParentId."', '".$Kind."', '".$Name."', '".$Value."'
            );";
  mysql_query($query,$Connection);
  return mysql_insert_id($Connection);
}

function insert_element($Table,$Connection,$ChildOf,$Name,$Value) {
  return insert_structural($Table,$Connection,$ChildOf,"element",$Name,$Value);
}

function insert_attribute($Table,$Connection,$ChildOf,$Name,$Value) {
  return insert_structural($Table,$Connection,
            $ChildOf,"attribute",$Name,$Value);
}

function insert_comment($Table,$Connection,$ChildOf,$Value) {
  return insert_structural($Table,$Connection,$ChildOf,"comment","",$Value);
}


function normalize_xml_node($Table,$Connection,
  $ParentId,$Node,$Connection,$HasTextPs) {
  foreach ($Node->childNodes as $Child) {
    if ($Child->nodeType == XML_ELEMENT_NODE) {
      $TagName = $Child->tagName;
      $Serialize = in_array($TagName,$HasTextPs);
      $Attributes = $Child->attributes;
      $Value = "";
      if ($Serialize) {
        $sxml = simplexml_import_dom($Child);
        $Value = $sxml->asXML();
      }
      $Child_Id = insert_element($Table,$Connection,$ParentId,$TagName,$Value);
      echo "<p><b>".$TagName."</b></p>";
      for ($adx = 0; $adx < $Attributes->length; $adx++) {
        $Attribute = $Attributes->item($adx);
        $Name = $Attribute->nodeName;
        $Value = $Attribute->nodeValue;
      }
      normalize_xml_node($Table,$Connection,
        $Child_Id,$Child,$Connection,$HasTextPs);
    }
  }
}

function normalize_xml($Table,$Connection,
  $DOMDocument,$Connection,$HasTextPs) {
  $Node = $DOMDocument->ownerDocument;
  normalize_xml_node($Table,$Connection,-1,$Node,$Connection,$HasTextPs);
}

function empty_all_xml($Connection) {
  $query = "TRUNCATE TABLE `Structural`";
  mysql_query($query,$Connection);

}

function test() {
  $ApathyName = "Apathy.xml";
  $ApathyDom = get_apathy_dom($ApathyName);
  $Connection = create_connection("ApathyRPG");
  $HasTextPs = array("title","text","define",
                      "mn","mo","mi",
                      "num","face","bns","bOff",
                      "rOff","raw","kind","mul");
  normalize_xml("ApathyRPG",$Connection,$ApathyDom,$Connection,$HasTextPs);
  //empty_all_xml($Connection);
}

test();

?>