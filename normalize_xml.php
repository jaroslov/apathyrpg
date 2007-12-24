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

function insert_structural($Table,$Connection,
  $ParentId,$Kind,$Order,$Name,$Value) {
  $query = "INSERT INTO `".$Table."`.`Structural` (
            `ID`, `ChildOf`, `Kind`, `Order`, `Name`, `Value`
            ) VALUES (
              NULL , '".$ParentId."', '".$Kind."',
              '".$Order."', '".$Name."', '".$Value."'
            );";
  mysql_query($query,$Connection);
  return mysql_insert_id($Connection);
}

function insert_element($Table,$Connection,$ChildOf,$Order,$Name,$Value) {
  return insert_structural($Table,$Connection,
    $ChildOf,"element",$Order,$Name,$Value);
}

function insert_attribute($Table,$Connection,$ChildOf,$Name,$Value) {
  return insert_structural($Table,$Connection,
            $ChildOf,"attribute",-1,$Name,$Value);
}

function insert_comment($Table,$Connection,$ChildOf,$Order,$Value) {
  return insert_structural($Table,$Connection,
    $ChildOf,"comment",$Order,"",$Value);
}


function normalize_xml_node($Table,$Connection,
  $ParentId,$Node,$Connection,$HasTextPs,$DropId) {
  $Order = 0;
  foreach ($Node->childNodes as $Child) {
    if ($Child->nodeType == XML_ELEMENT_NODE) {
      $Order++;
      $TagName = $Child->tagName;
      $Serialize = in_array($TagName,$HasTextPs);
      $Attributes = $Child->attributes;
      $Value = "";
      if ($Serialize) {
        $sxml = simplexml_import_dom($Child);
        $Value = $sxml->asXML();
      }
      $Child_Id = insert_element($Table,$Connection,
        $ParentId,$Order,$TagName,$Value);
      for ($adx = 0; $adx < $Attributes->length; $adx++) {
        $Attribute = $Attributes->item($adx);
        $Name = $Attribute->nodeName;
        $Value = $Attribute->nodeValue;
        if ($DropId and ($Name === "xml:id" or $Name === "id"))
          continue;
        insert_attribute($Table,$Connection,$ParentId,$Name,$Value);
      }
      if (!$Serialize) {
        normalize_xml_node($Table,$Connection,
          $Child_Id,$Child,$Connection,$HasTextPs,$DropId);
      }
    }
  }
}

function normalize_xml($Table,$Connection,
  $DOMDocument,$Connection,$HasTextPs,$DropId) {
  $Node = $DOMDocument->ownerDocument;
  normalize_xml_node($Table,$Connection,
    -1,$Node,$Connection,$HasTextPs,$DropId);
}

function empty_all_xml($Connection) {
  $query = "TRUNCATE TABLE `Structural`";
  mysql_query($query,$Connection);
}

?>