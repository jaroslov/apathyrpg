<?php

// Normalize XML
// convert an XML document into a database
include 'config.php';

function xmldb_create_connection($Database) {
  // Connecting, selecting database
  $link = mysql_connect('localhost', 'thechao', 'ha1l3r1S')
      or die('Could not connect: ' . mysql_error());  
  if (mysql_select_db($Database))
    return $link;
  return 'Could not select database';
}

function xmldb_sanitize_for_xml($String) {
  $String = str_replace("'","&apos;",$String);
  return $String;
}

function xmldb_sanitize_for_sql($String) {
  $String = str_replace("'","''",$String);
  return $String;
}

function xmldb_insert_structural($Table,$Connection,
  $ParentId,$Kind,$Order,$Name,$Value) {
  $Kind = xmldb_sanitize_for_xml($Kind);
  $Name = xmldb_sanitize_for_xml($Name);
  $Value = xmldb_sanitize_for_xml($Value);
  $query = "INSERT INTO `".$Table."`.`Structural` (
            `ID`, `ChildOf`, `Kind`, `Order`, `Name`, `Value`
            ) VALUES (
              NULL , '".$ParentId."', '".$Kind."',
              '".$Order."', '".$Name."', '".$Value."'
            );";
  mysql_query($query,$Connection) or die('Query failed: ' . mysql_error());;
  return mysql_insert_id($Connection);
}

function xmldb_insert_element($Table,$Connection,$ChildOf,$Order,$Name,$Value) {
  return xmldb_insert_structural($Table,$Connection,
    $ChildOf,"element",$Order,$Name,$Value);
}

function xmldb_insert_attribute($Table,$Connection,$ChildOf,$Name,$Value) {
  return xmldb_insert_structural($Table,$Connection,
            $ChildOf,"attribute",-1,$Name,$Value);
}

function xmldb_insert_comment($Table,$Connection,$ChildOf,$Order,$Value) {
  return xmldb_insert_structural($Table,$Connection,
    $ChildOf,"comment",$Order,"",$Value);
}

function xmldb_serialize_as_raw_text($Node) {
  $result = "";
  foreach ($Node->childNodes as $Child)
    switch ($Child->nodeType) {
    case XML_TEXT_NODE: $result .= $Child->nodeValue; break;
    case XML_ELEMENT_NODE:
      $sxml = simplexml_import_dom($Child);
      $result .= $sxml->asXML();
      break;
    default: "{UNKNOWN-NODE-TYPE}"; break;
    }
  $result = trim($result);
  $words = split("[\t\n\r ]+", $result);
  return implode(" ",$words);
}

function xmldb_normalize_xml_node($DOMDocument,$Table,$Connection,
  $ParentId,$Node,$Connection,$HasTextPs,$DropId) {
  $Order = 0;
  foreach ($Node->childNodes as $Child) {
    if ($Child->nodeType == XML_ELEMENT_NODE) {
      $Order++;
      $TagName = $Child->tagName;
      $Serialize = in_array($TagName,$HasTextPs);
      $Attributes = $Child->attributes;
      $Value = "";
      if ($Serialize)
        $Value = xmldb_serialize_as_raw_text($Child);
      $ChildId = xmldb_insert_element($Table,$Connection,
        $ParentId,$Order,$TagName,$Value);
      for ($adx = 0; $adx < $Attributes->length; $adx++) {
        $Attribute = $Attributes->item($adx);
        $Name = $Attribute->nodeName;
        $Value = $Attribute->nodeValue;
        if (!($DropId and ($Name === "xml:id" or $Name === "id")))
          xmldb_insert_attribute($Table,$Connection,$ChildId,$Name,$Value);
      }
      if (!$Serialize) {
        xmldb_normalize_xml_node($DOMDocument,$Table,$Connection,
          $ChildId,$Child,$Connection,$HasTextPs,$DropId);
      }
    }
  }
}

function xmldb_create_kind_view($Table,$Connection,$KTable,$Kind) {
  $query = "CREATE VIEW ".$KTable." AS SELECT * FROM `Structural`
            WHERE `Kind` = '".$Kind."';";
  mysql_query($query,$Connection);
}

function xmldb_create_attribute_view($Table,$Connection) {
  $query = "CREATE VIEW attributes AS SELECT * FROM `Structural`
            WHERE `Kind` = 'attribute';";
  mysql_query($query,$Connection);
}

function xmldb_normalize_xml($Table,$Connection,
  $DOMDocument,$Connection,$HasTextPs,$DropId) {
  //$Node = $DOMDocument->ownerDocument;
  xmldb_normalize_xml_node($DOMDocument,$Table,$Connection,
    -1,$DOMDocument->ownerDocument,$Connection,$HasTextPs,$DropId);
  // need views of elements and attributes
  xmldb_create_kind_view($Table,$Connection,"Attributes","attribute");
  xmldb_create_kind_view($Table,$Connection,"Elements","element");
  xmldb_create_kind_view($Table,$Connection,"Comments","comment");
}

function xmldb_extract_xml($Connection) {
  $query = "SELECT * FROM `Structural`";
  $resource = mysql_query($query,$Connection);
  $DOM = new DOMDocument();
  return $DOM;
}

function xmldb_empty_all_xml($Connection) {
  $query = "TRUNCATE TABLE `Structural`";
  mysql_query($query,$Connection);
}

function xmldb_getElementById($Connection,$ID) {
  $query = "SELECT * FROM `Structural` WHERE `ID` =".$ID;
  $resource = mysql_query($query,$Connection);
  $record = array();
  if ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_getAttributeById($Connection,$ID) {
  $query = "SELECT * FROM `Attributes` WHERE `ID` =".$ID;
  $resource = mysql_query($query,$Connection);
  $record = array();
  if ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
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

function xmldb_setElementValue($Connection,$Element,$Value) {
  $Value = xmldb_sanitize_for_sql($Value);
  $query = "UPDATE `Structural` SET
            `Value` ='".$Value."'
            WHERE `Structural`.`ID` =".$Element["ID"]." LIMIT 1 ;";
  mysql_query($query,$Connection);
}

function xmldb_setNodeValue($Connection,$ID,$Value) {
  $Value = xmldb_sanitize_for_sql($Value);
  $query = "UPDATE `Structural` SET
            `Value` ='".$Value."'
            WHERE `Structural`.`ID` =".$ID." LIMIT 1 ;";
  if (!mysql_query($query,$Connection))
    return array("Error"=>mysql_error());
  return array("Error"=>"none");
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

function xmldb_attributesOfSet($Connection,$Elements) {
  $ElementSet = "";
  $Keys = array_keys($Elements);
  if (sizeof($Elements) > 0)
    $ElementSet .= $Elements[$Keys[0]]["ID"];
  for ($edx = 1; $edx < sizeof($Elements); $edx++)
    $ElementSet .= "," . $Elements[$Keys[$edx]]["ID"];
  $query = "SELECT * FROM `Structural`
            WHERE `ChildOf` IN (".$ElementSet.")
            AND `Kind` = CONVERT( _utf8 'attribute' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  $attributeset = array();
  while ($record = mysql_fetch_array($resource))
    if (in_array($record["ChildOf"],array_keys($attributeset)))
      $attributeset[$record["ChildOf"]][$record["Name"]]
        = xmldb_convert_record($record);
    else
      $attributeset[$record["ChildOf"]]
        = array($record["Name"]=>xmldb_convert_record($record));
  return $attributeset;
}

function xmldb_getChildNodeValuesOfSet($Connection,$Elements) {
  $ElementSet = "";
  $Keys = array_keys($Elements);
  $ValueSet = array();
  if (sizeof($Elements) > 0) {
    $ElementSet .= $Elements[$Keys[0]]["ID"];
    $ValueSet[$Elements[$Keys[0]]["ID"]] = array();
  }
  for ($edx = 1; $edx < sizeof($Elements); $edx++) {
    $ElementSet .= "," . $Elements[$Keys[$edx]]["ID"];
    $ValueSet[$Elements[$Keys[$edx]]["ID"]] = array();
  }
  $query = "SELECT * FROM `Structural`
            WHERE `ChildOf` IN (".$ElementSet.")
            AND `Kind` = CONVERT( _utf8 'element' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    $ValueSet[$record["ChildOf"]][$record["ID"]]
        = xmldb_convert_record($record);
  return $ValueSet;
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

function xmldb_setAttribute($Connection,$Attribute,$Value) {
  $query = "UPDATE `Structural` SET
            `Value` = '".$Value."'
            WHERE `Structural`.`ID` =".$Attribute["ID"]." LIMIT 1 ;";
  mysql_query($query,$Connection);
}

function xmldb_getChildNodes($Connection,$Node) {
  $query = "SELECT * FROM `Elements` WHERE `ChildOf`=".$Node["ID"];
  $resource = mysql_query($query,$Connection);
  $children = array();
  while ($record = mysql_fetch_array($resource))
    $children[$record["ID"]] = xmldb_convert_record($record);
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