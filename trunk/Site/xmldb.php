<?php

// Normalize XML
// convert an XML document into a database
include 'config.php';

// shorthand
define("XMLDB_DBT","`".XMLDB_DBName."`.`".XMLDB_MainTable."`",true);
define("XMLDB_History","History",true);
define("XMLDB_DBH","`".XMLDB_DBName."`.`".XMLDB_History."`",true);
define("XMLDB_Attributes","`Attributes`",true);
define("XMLDB_Elements","`Elements`",true);
define("XMLDB_Comments","`Comments`",true);
define("XMLDB_DBA","`".XMLDB_DBName."`.`Attributes`",true);
define("XMLDB_DBE","`".XMLDB_DBName."`.`Elements`",true);
define("XMLDB_DBC","`".XMLDB_DBName.".`Comment`",true);

function xmldb_create_connection() {
  // Connecting, selecting database
  $link = mysql_connect(XMLDB_HostTarget, XMLDB_DBUser, XMLDB_DBPass)
      or die('Could not connect: ' . mysql_error());
  $query = "CREATE DATABASE `".XMLDB_DBName."` ;";
  mysql_query($query,$link);
  if (mysql_select_db(XMLDB_DBName))
    return $link;
  return 'Could not select database';
}

function xmldb_create_main_table($Connection) {
  return xmldb_create_table($Connection,XMLDB_MainTable);
}
function xmldb_create_history_table($Connection) {
  return xmldb_create_table($Connection,XMLDB_History);
}

function xmldb_create_table($Connection,$Name) {
  $query = "CREATE TABLE  `".XMLDB_DBName."`.`".$Name."` (
              `ID` INT(10)
                  NOT NULL AUTO_INCREMENT ,
              `ChildOf` INT(10)
                  NOT NULL DEFAULT  '-1',
              `Kind` ENUM('element',
                          'attribute',
                          'comment')
                  NOT NULL DEFAULT  'element',
              `Order` INT(10)
                  NOT NULL DEFAULT  '-1',
              `Name` VARCHAR(255)
                  NOT NULL DEFAULT  'default-tag-name',
              `Value`
                  TEXT NULL ,
              `TimeStamp` TIMESTAMP
                  NOT NULL DEFAULT CURRENT_TIMESTAMP ,
              PRIMARY KEY (`ID`)
            ) ENGINE = MYISAM";
  mysql_query($query,$Connection) or die("Table creation ".mysql_error());
}

function xmldb_sanitize_for_xml($String) {
  $String = str_replace("'","&apos;",$String);
  return $String;
}

function xmldb_sanitize_for_sql($String) {
  $String = str_replace("'","''",$String);
  return $String;
}

function xmldb_insert_structural($Connection,$Record) {
  $record = array();
  foreach ($Record as $key => $value)
    $record[$key] = xmldb_sanitize_for_sql($value);
  $query = "INSERT INTO  ".XMLDB_DBT." (
            `ID`,`ChildOf`,`Kind`,`Order`,`Name`,`Value`,`TimeStamp`
            ) VALUES (
              NULL,
              '".$record["ChildOf"]."',
              '".$record["Kind"]."',
              '".$record["Order"]."',
              '".$record["Name"]."',
              '".$record["Value"]."', 
              CURRENT_TIMESTAMP
            );";
  mysql_query($query,$Connection) or die('Query failed: ' . mysql_error());;
  return mysql_insert_id($Connection);
}

function xmldb_insert_history($Connection,$Record) {
  $record = array();
  foreach ($Record as $key => $value)
    $record[$key] = xmldb_sanitize_for_sql($value);
  $query = "INSERT INTO  ".XMLDB_DBH." (
            `ID`,`ChildOf`,`Kind`,`Order`,`Name`,`Value`,`TimeStamp`
            ) VALUES (
              NULL,
              '".$record["ChildOf"]."',
              '".$record["Kind"]."',
              '".$record["Order"]."',
              '".$record["Name"]."',
              '".$record["Value"]."', 
              '".$record["TimeStamp"]."'
            );";
  mysql_query($query,$Connection) or die('Query failed: ' . mysql_error());;
  return mysql_insert_id($Connection);
}

function xmldb_remove_node($Connection,$ID) {
  $query ="DELETE FROM ".XMLDB_DBT." WHERE ".XMLDB_DBT.".`ID` = $ID";
  return mysql_query($query,$Connection);
}


function xmldb_insert_element($Connection,$ChildOf,$Order,$Name,$Value) {
  return xmldb_insert_structural($Connection,
      array("ChildOf"=>$ChildOf,
            "Kind"=>"element",
            "Order"=>$Order,
            "Name"=>$Name,
            "Value"=>$Value));
}

function xmldb_insert_attribute($Connection,$ChildOf,$Name,$Value) {
  return xmldb_insert_structural($Connection,
      array("ChildOf"=>$ChildOf,
            "Kind"=>"attribute",
            "Order"=>-1,
            "Name"=>$Name,
            "Value"=>$Value));
}

function xmldb_insert_comment($Connection,$ChildOf,$Order,$Value) {
  return xmldb_insert_structural($Connection,
    array("ChildOf"=>$ChildOf,
          "Kind"=>"comment",
          "Order"=>$Order,
          "Name"=>$Name,
          "Value"=>$Value));
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

function xmldb_normalize_xml_node($DOMDocument,$Connection,$ParentId,$Node,$Connection,$HasTextPs,$DropId) {
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
      $ChildId = xmldb_insert_element($Connection,$ParentId,$Order,$TagName,$Value);
      for ($adx = 0; $adx < $Attributes->length; $adx++) {
        $Attribute = $Attributes->item($adx);
        $Name = $Attribute->nodeName;
        $Value = $Attribute->nodeValue;
        if (!($DropId and ($Name === "xml:id" or $Name === "id")))
          xmldb_insert_attribute($Connection,$ChildId,$Name,$Value);
      }
      if (!$Serialize) {
        xmldb_normalize_xml_node($DOMDocument,$Connection,$ChildId,$Child,$Connection,$HasTextPs,$DropId);
      }
    }
  }
}

function xmldb_create_kind_view($Connection,$KTable,$Kind) {
  $query = "CREATE VIEW `".XMLDB_DBName."`.".$KTable."
            AS SELECT * FROM `".XMLDB_MainTable."`
            WHERE `Kind` = '".$Kind."';";
  mysql_query($query,$Connection);
}

function xmldb_normalize_xml($Connection,$DOMDocument,$Connection,$HasTextPs,$DropId) {
  xmldb_create_main_table($Connection);
  xmldb_create_history_table($Connection);
  xmldb_normalize_xml_node($DOMDocument,$Connection,-1,$DOMDocument->ownerDocument,$Connection,$HasTextPs,$DropId);
  // need views of elements and attributes
  xmldb_create_kind_view($Connection,"Attributes","attribute");
  xmldb_create_kind_view($Connection,"Elements","element");
  xmldb_create_kind_view($Connection,"Comments","comment");
}

function xmldb_extract_xml($Connection) {
  $query = "SELECT * FROM ".XMLDB_DBT;
  $resource = mysql_query($query,$Connection);
  $DOM = new DOMDocument();
  return $DOM;
}

function xmldb_empty_all_xml($Connection) {
  $query = "TRUNCATE TABLE ".XMLDB_DBT;
  mysql_query($query,$Connection);
}

function xmldb_getElementById($Connection,$ID) {
  $query = "SELECT * FROM ".XMLDB_DBT." WHERE `ID` =".$ID;
  $resource = mysql_query($query,$Connection);
  $record = array();
  if ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_getAttributeById($Connection,$ID) {
  $query = "SELECT * FROM ".XMLDB_DBA." WHERE `ID` =".$ID;
  $resource = mysql_query($query,$Connection);
  $record = array();
  if ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_removeAttributeById($Connection,$ID) {
  $oldattr = xmldb_getElementById($Connection,$ID);
  xmldb_insert_history($Connection,$oldattr);
  xmldb_remove_node($Connection,$ID);
}

function xmldb_getNodeById($Connection,$ID) {
  $query = "SELECT * FROM ".XMLDB_DBT." WHERE `ID` = ".$ID;
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
                "Value"=>$record["Value"],
                "TimeStamp"=>$record["TimeStamp"]);
}

function xmldb_getElementsByTagName($Connection,$TagName) {
  $query = "SELECT * FROM ".XMLDB_DBT."
            WHERE `Kind` = CONVERT( _utf8 'element' USING latin1 )
            AND `Name` LIKE CONVERT( _utf8 '".$TagName."' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  $elements = array();
  while ($record = mysql_fetch_array($resource))
    $elements[$record["ID"]] = xmldb_convert_record($record);
  return $elements;
}

function xmldb_putAttributeById($Connection,$ID,$Attribute) {
  foreach ($Attribute as $Entry => $Value)
    $Attribute[$Entry] = xmldb_sanitize_for_sql($Value);
  $oldvalue = xmldb_getElementById($Connection,$ID);
  xmldb_insert_history($Connection,$oldvalue);
  return xmldb_insert_attribute($Connection,
                                $oldvalue["ChildOf"],// always maintain parent
                                $Attribute["Name"],
                                $Attribute["Value"]);
}

function xmldb_setElement($Connection,$Record) {
  $oldvalue = xmldb_getElementById($Connection,$ID);
  xmldb_insert_history($Connection,$oldvalue);
  $query = "UPDATE ".XMLDB_DBT." SET
            `ChildOf` = '".$Record["ChildOf"]."',
            `Order` = '".$Record["Order"]."',
            `Name` = '".$Record["Name"]."',
            `Value` = '".$Record["Value"]."',
            `TimeStamp` = CURRENT_TIMESTAMP
            WHERE ".XMLDB_DBT.".`ID` = ".$Record["ID"]." LIMIT 1;";
  return mysql_query($query,$Connection);
}

function xmldb_setElementNV($Connection,$ID,$Name,$Value) {
  $oldvalue = xmldb_getElementById($Connection,$ID);
  xmldb_insert_history($Connection,$oldvalue);
  $oldvalue["Name"] = $Name;
  $oldvalue["Value"] = $Value;
  return xmldb_setElement($Connection,$oldvalue);
}

function xmldb_setElementValueById($Connection,$ID,$Value) {
  $Value = xmldb_sanitize_for_sql($Value);
  $oldvalue = xmldb_getElementById($Connection,$ID);
  xmldb_insert_history($Connection,$oldvalue);
  $query = "UPDATE ".XMLDB_DBT." SET
            `Value` ='".$Value."'
            WHERE ".XMLDB_DBT.".`ID` =".$ID." LIMIT 1 ;";
  mysql_query($query,$Connection);
}

function xmldb_setNodeValueById($Connection,$ID,$Value) {
  $Value = xmldb_sanitize_for_sql($Value);
  $oldvalue = xmldb_getElementById($Connection,$ID);
  xmldb_insert_history($Connection,$oldvalue);
  $query = "UPDATE ".XMLDB_DBT." SET
            `Value` ='".$Value."'
            WHERE ".XMLDB_DBT.".`ID` =".$ID." LIMIT 1 ;";
  if (!mysql_query($query,$Connection))
    return array("Error"=>mysql_error());
  return array("Error"=>"none");
}

function xmldb_attributes($Connection,$Element) {
  if (is_array($Element))
    $Element = $Element["ID"];
  $query = "SELECT * FROM ".XMLDB_DBT."
            WHERE `ChildOf` = ".$Element."
            AND `Kind` = CONVERT( _utf8 'attribute' USING latin1 )";
  $resource = mysql_query($query);
  $attributes = array();
  while ($record = mysql_fetch_array($resource))
    $attributes[$record["ID"]] = xmldb_convert_record($record);
  return $attributes;
}

function xmldb_attributesOfSet($Connection,$Elements) {
  $ElementSet = "";
  $Keys = array_keys($Elements);
  if (sizeof($Elements) > 0)
    $ElementSet .= $Elements[$Keys[0]]["ID"];
  for ($edx = 1; $edx < sizeof($Elements); $edx++)
    $ElementSet .= "," . $Elements[$Keys[$edx]]["ID"];
  $query = "SELECT * FROM ".XMLDB_DBA."
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

function xmldb_getElementIdsByIdOfSet($Connection,$IdSet) {
  $id_set = implode(", ",$IdSet);
  $query = "SELECT `ID` FROM ".XMLDB_DBT."
            WHERE `ChildOf` IN (".$id_set.")";
  $resource = mysql_query($query,$Connection);
  $NIdSet = array();
  while ($record = mysql_fetch_array($resource))
    array_push($NIdSet,$record["ID"]);
  return $NIdSet;
}

function xmldb_getNodesOfSet($Connection,$IdSet) {
  $id_set = implode(", ",$IdSet);
  $query = "SELECT * FROM ".XMLDB_DBT."
            WHERE `ID` IN (".$id_set.")";
  $resource = mysql_query($query,$Connection);
  $EltSet = array();
  while ($record = mysql_fetch_array($resource))
    $EltSet[$record["ID"]] = xmldb_convert_record($record);
  return $EltSet;
}

function xmldb_getChildNodesOfSet($Connection,$IdSet) {
  $id_set = implode(", ",$IdSet);
  $query = "SELECT * FROM ".XMLDB_DBT."
            WHERE `ChildOf` IN (".$id_set.")";
  $resource = mysql_query($query,$Connection);
  $EltSet = array();
  while ($record = mysql_fetch_array($resource))
    array_push($EltSet,xmldb_convert_record($record));
  return $EltSet;
}

function xmldb_getElementsOfSet($Connection,$IdSet) {
  $id_set = implode(", ",$IdSet);
  $query = "SELECT * FROM ".XMLDB_DBT."
            WHERE `ChildOf` IN (".$id_set.")";
  $resource = mysql_query($query,$Connection);
  $EltSet = array();
  while ($record = mysql_fetch_array($resource))
    $EltSet[$record["ID"]] = xmldb_convert_record($record);
  return $EltSet;
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
  $query = "SELECT * FROM ".XMLDB_DBE."
            WHERE `ChildOf` IN (".$ElementSet.")
            AND `Kind` = CONVERT( _utf8 'element' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    $ValueSet[$record["ChildOf"]][$record["ID"]]
        = xmldb_convert_record($record);
  return $ValueSet;
}

function xmldb_getElementsByTagNameOfSet($Connection,$Elements,$TagName) {
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
  $query = "SELECT * FROM ".XMLDB_DBE."
            WHERE `ChildOf` IN (".$ElementSet.")
            AND `Kind` = CONVERT( _utf8 'element' USING latin1 )
            AND `Name` = CONVERT( _utf8 '$TagName' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    $ValueSet[$record["ChildOf"]][$record["ID"]]
        = xmldb_convert_record($record);
  return $ValueSet;
}

function xmldb_getAttribute($Connection,$Element,$Name) {
  $query = "SELECT * FROM ".XMLDB_DBT."
            WHERE `ChildOf` = ".$Element["ID"]."
            AND `Kind` = CONVERT( _utf8 'attribute' USING latin1 )
            AND `Name` = CONVERT( _utf8 '".$Name."' USING latin1 )";
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_getAttributeOfTagName($Connection,$TagName,$AttrName) {
  $query = "SELECT * FROM ".XMLDB_DBT.",".XMLDB_DBA."
            WHERE ".XMLDB_DBT.".`ID`=`Attributes`.`ChildOf`
            AND ".XMLDB_DBT.".`Name` = '".$TagName."'
            AND ".XMLDB_DBA.".`Name` = '".$AttrName."';";
  $resource = mysql_query($query,$Connection);
  $attributes = array();
  while ($record = mysql_fetch_array($resource))
    array_push($attributes,xmldb_convert_record($record));
  return $attributes;
}

function xmldb_getAttributeOfAllChildren($Connection,$Parent,$ChildName,$AttrName) {
  $query = "SELECT * FROM ".XMLDB_DBT.",".XMLDB_DBA."
            WHERE ".XMLDB_DBT.".`ChildOf` = '".$Parent["ID"]."'
            AND ".XMLDB_DBT.".`ID`=`Attributes`.`ChildOf`
            AND ".XMLDB_DBT.".`Name` = '".$ChildName."'
            AND ".XMLDB_DBA.".`Name` = '".$AttrName."';";
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
  $query = "SELECT * FROM ".XMLDB_DBT."
            WHERE `ID` = ".$ID;
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    return xmldb_convert_record($record);
  return false;
}

function xmldb_getChildNodes($Connection,$ID) {
  if (is_array($ID))
    $ID = $ID["ID"];
  $query = "SELECT * FROM ".XMLDB_DBT." WHERE `ChildOf`=".$ID;
  $resource = mysql_query($query,$Connection);
  $children = array();
  while ($record = mysql_fetch_array($resource))
    $children[$record["ID"]] = xmldb_convert_record($record);
  return $children;
}

function xmldb_is_populated($Connection) {
  $query = "SELECT * FROM ".XMLDB_DBT." WHERE `ID` =1";
  $resource = mysql_query($query,$Connection);
  while ($record = mysql_fetch_array($resource))
    return true;
  return false;
}

function xmldb_get_all_children($Connection,$InitialSet) {
  $all_ids = $InitialSet;
  $id_set = $InitialSet;
  $repetitions = 0;
  do {
    $old_size = sizeof($all_ids);
    $id_set = xmldb_getElementIdsByIdOfSet($Connection,$id_set);
    foreach ($id_set as $nid)
      array_push($all_ids,$nid);
  } while ($old_size !== sizeof($all_ids));
  sort($all_ids);
  $all_nodes = xmldb_getChildNodesOfSet($Connection,$all_ids);
  return $all_nodes;
}

function xmldb_get_document($Connection) {
  $query = "SELECT * FROM ".XMLDB_DBT;
  $resource = mysql_query($query,$Connection);
  $children = array();
  while ($record = mysql_fetch_array($resource))
    $children[$record["ID"]] = xmldb_convert_record($record);
  return $children;
}

function xmldb_child_table_of_elements($Elements) {
  $ChildOfTable = array();
  foreach ($Elements as $element) {
    if (array_key_exists($element["ChildOf"],$ChildOfTable))
      $ChildOfTable[$element["ChildOf"]][$element["ID"]] = $element;
    else
      $ChildOfTable[$element["ChildOf"]]
        = array($element["ID"] => $element);
  }
  return $ChildOfTable;
}

function xmldb_child_table_of_id($Connection,$Id) {
  $id_set = array($Id);
  $nodes = xmldb_get_all_children($Connection,$id_set);
  return xmldb_child_table_of_elements($nodes);
}

function xmldb_child_table_of_tagname($Connection,$TagName) {
  $which = xmldb_getElementsByTagName($Connection,$what);
  $id_set = array_keys($which);
  $nodes = xmldb_get_all_children($Connection,$id_set);
  return xmldb_child_table_of_elements($nodes);
}

function xmldb_child_table_of_document($Connection) {
  $document = xmldb_get_document($Connection);
  return xmldb_child_table_of_elements($document);
}

function xmldb_cot_attributes($CoTable,$Key) {
  $attrs = array();
  foreach ($CoTable[$Key] as $Id => $Child)
    if ($Child["Kind"] === "attribute")
      $attrs[$Child["Name"]] = $Child["Value"];
  return $attrs;
}

function xmldb_cot_childNodes($CoTable,$Key) {
  $children = array();
  foreach ($CoTable[$Key] as $Id => $Child)
    if ($Child["Kind"] === "element")
      $children[$Child["Order"]] = $Child;
  return $children;
}

?>