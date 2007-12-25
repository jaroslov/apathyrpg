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
      $ChildId = insert_element($Table,$Connection,
        $ParentId,$Order,$TagName,$Value);
      for ($adx = 0; $adx < $Attributes->length; $adx++) {
        $Attribute = $Attributes->item($adx);
        $Name = $Attribute->nodeName;
        $Value = $Attribute->nodeValue;
        if (!($DropId and ($Name === "xml:id" or $Name === "id")))
          insert_attribute($Table,$Connection,$ChildId,$Name,$Value);
      }
      if (!$Serialize) {
        normalize_xml_node($Table,$Connection,
          $ChildId,$Child,$Connection,$HasTextPs,$DropId);
      }
    }
  }
}

function create_kind_view($Table,$Connection,$KTable,$Kind) {
  $query = "CREATE VIEW ".$KTable." AS SELECT * FROM `Structural`
            WHERE `Kind` = '".$Kind."';";
  mysql_query($query,$Connection);
}

function create_attribute_view($Table,$Connection) {
  $query = "CREATE VIEW attributes AS SELECT * FROM `Structural`
            WHERE `Kind` = 'attribute';";
  mysql_query($query,$Connection);
}

function normalize_xml($Table,$Connection,
  $DOMDocument,$Connection,$HasTextPs,$DropId) {
  $Node = $DOMDocument->ownerDocument;
  normalize_xml_node($Table,$Connection,
    -1,$Node,$Connection,$HasTextPs,$DropId);
  // need views of elements and attributes
  create_kind_view($Table,$Connection,"Attributes","attribute");
  create_kind_view($Table,$Connection,"Elements","element");
  create_kind_view($Table,$Connection,"Comments","comment");
}

function xmldb_empty_all_xml($Connection) {
  $query = "TRUNCATE TABLE `Structural`";
  mysql_query($query,$Connection);
}

function xmldb_getElementById($Connection,$ID) {
  $query = "SELECT * FROM `Elements` WHERE `ID` =".$ID;
  $resource = mysql_query($query,$Connection);
  if ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($resource);
  return false;
}

function xmldb_getAttributeById($Connection,$ID) {
  $query = "SELECT * FROM `Attributes` WHERE `ID` =".$ID;
  $resource = mysql_query($query,$Connection);
  if ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($resource);
  return false;
}

function xmldb_getNodeById($Connection,$ID) {
  $query = "SELECT * FROM `Structural` WHERE `ID` = ".$ID;
  $resource = mysql_query($query,$Connection);
  $record = array();
  if ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_convert_record($record) {
  return array("ID"=>$record["ID"],
                "ChildOf"=>$record["ChildOf"],
                "Kind"=>$record["Kind"],
                "Order"=>$record["Order"],
                "Name"=>$record["Name"],
                "Value"=>$record["Value"]);
}

function xmldb_getElementsByTagName($Connection,$TagName) {
  $query = "SELECT * FROM `Structural`
            WHERE `Kind` = CONVERT( _utf8 'element' USING latin1 )
            AND `Name` LIKE CONVERT( _utf8 '".$TagName."' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  $elements = array();
  while ($record = mysql_fetch_array($resource))
    array_push($elements,xmldb_convert_record($record));
  return $elements;
}

function xmldb_table_setElementValue($Table,$Connection,$Element,$Value) {
  $query = "UPDATE `".$Table."`.`Structural` SET
            `Value` ='".$Value."'
            WHERE `Structural`.`ID` =".$Element["ID"]." LIMIT 1 ;";
  mysql_query($query,$Connection);
}

function xmldb_setElementValue($Connection,$Element,$Value) {
  $query = "UPDATE `Structural` SET
            `Value` ='".$Value."'
            WHERE `Structural`.`ID` =".$Element["ID"]." LIMIT 1 ;";
  mysql_query($query,$Connection);
}

function xmldb_attributes($Connection,$Element) {
  $query = "SELECT * FROM `Structural`
            WHERE `ChildOf` = ".$Element["ID"]."
            AND `Kind` = CONVERT( _utf8 'attribute' USING latin1 )";
  $resource = mysql_query($query);
  $attributes = array();
  while ($record = mysql_fetch_array($resource))
    array_push($attributes,xmldb_convert_record($record));
  return $attributes;
}

function xmldb_getAttribute($Connection,$Element,$Name) {
  $query = "SELECT * FROM `Structural`
            WHERE `ChildOf` = ".$Element["ID"]."
            AND `Kind` = CONVERT( _utf8 'attribute' USING latin1 )
            AND `Name` = CONVERT( _utf8 '".$Name."' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_getAttributeOfTagName($Connection,$TagName,$AttrName) {
  $query = "SELECT * FROM `Structural`,`Attributes`
            WHERE `Structural`.`ID`=`Attributes`.`ChildOf`
            AND `Structural`.`Name` = '".$TagName."'
            AND `Attributes`.`Name` = '".$AttrName."';";
  $resource = mysql_query($query,$Connection);
  $attributes = array();
  while ($record = mysql_fetch_array($resource))
    array_push($attributes,xmldb_convert_record($record));
  return $attributes;
}


function xmldb_getAttributeOfAllChildren($Connection,$Parent,$ChildName,$AttrName) {
  $query = "SELECT * FROM `Structural`,`Attributes`
            WHERE `Structural`.`ChildOf` = '".$Parent["ID"]."'
            AND `Structural`.`ID`=`Attributes`.`ChildOf`
            AND `Structural`.`Name` = '".$ChildName."'
            AND `Attributes`.`Name` = '".$AttrName."';";
  $resource = mysql_query($query,$Connection);
  $attributes = array();
  while ($record = mysql_fetch_array($resource))
    array_push($attributes,xmldb_convert_record($record));
  return $attributes;
}

function xmldb_getParent($Connection,$Node) {
  return xmldb_getParentById($Connection,$Node["ChildOf"]);
}

function xmldb_getParentById($Connection,$ID) {
  $query = "SELECT * FROM `Structural`
            WHERE `ID` = ".$ID;
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_table_setAttribute($Table,$Connection,$Attribute,$Value) {
  $query = "UPDATE `".$Table."`.`Structural` SET
            `Value` = '".$Value."'
            WHERE `Structural`.`ID` =".$Attribute["ID"]." LIMIT 1 ;";
  mysql_query($query,$Connection);
}

function xmldb_setAttribute($Connection,$Attribute,$Value) {
  $query = "UPDATE `Structural` SET
            `Value` = '".$Value."'
            WHERE `Structural`.`ID` =".$Attribute["ID"]." LIMIT 1 ;";
  mysql_query($query,$Connection);
}

function xmldb_getChildNodes($Connection,$Node) {
  $query = "SELECT * FROM `Structural` WHERE `ChildOf`=".$Node["ID"];
  $resource = mysql_query($query,$Connection);
  $children = array();
  while ($record = mysql_fetch_array($resource))
    array_push($children,xmldb_convert_record($record));
  return $children;
}

function xmldb_is_populated($Connection) {
  $query = "SELECT * FROM `Structural` WHERE `ID` =1";
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    return true;
  return false;
}

?>