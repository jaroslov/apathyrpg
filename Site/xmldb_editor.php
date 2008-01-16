<?php

include_once "config.php";
include_once "xmldb.php";
include_once "ajax.php";

function xod_translate_for_display($Text) {
  $Text = preg_replace("/\&/","&amp;",$Text);
  $Text = preg_replace("/\</","&lt;",$Text);
  $Text = preg_replace("/\>/","&gt;",$Text);
  $Text = preg_replace("/\'/","&apos;",$Text);
  $Text = preg_replace("/\"/","&quot;",$Text);
  return $Text;
}

function xod_build_element($node,$RenderDepth) {
  $element_fragment = file_get_contents("display_element.xmlf");

  $MMBut = "+";
  if ($RenderDepth==0)
    $MMBut = "&#8211;";

  $elt_vals = array(
    "target" => $node["ID"],
    "tagName" => $node["Name"],
    "MMButton" => $MMBut,
    "childrenText" => 'foo');
}

function xod_render_context($CoTable,$RenderContext,
          $RenderDepth,$node,$attributes,$childNodes) {
  $Id = $node["ID"];
  // build a table
  //  tag-name
  //    attr val    descr
  //    attr val
  //    children...
  $tagName = $node["Name"];
  $nodeValue = $node["Value"];

  $Text = "";
  if (strlen($nodeValue)>0)
    $Text = xod_translate_for_display($nodeValue);

  $onShowChildren = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php",
      array("LoadChildren"),
      array($Id))
    .";\"";

  $toggleChildren = "onclick=\"toggleVisibility('ENL$Id','none','block');
                              toggleMinimizeButton('MB$Id','ENL$Id');\"";

  $onModifyElement = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php",
      array("ModifyElement"),
      array($Id))
    .";\"";

  $MMBut = "+";
  if ($RenderDepth==0) {
    $MMBut = "&#8211;";
    $toggleChildren = xod_onclick_edit("LoadChildren","$Id");
  }

  $table  = "<ul id='Element$Id'>";
  $table .= "<li>";
  $table .= "<div class='xod-mm-button' id='MBCtr$Id'>
                <div id='MB$Id' $toggleChildren>$MMBut</div>
            </div>";
  $table .= "<div class='xod-tagname'
                  id='TN$Id' valign='top'
                  $onModifyElement>$tagName</div>";
  if ($RenderDepth == 0) {
    $table .= "<div class='xod-children' id='Children$Id'
                  valign='top' rowspan='2'>
                  <ul id='Children$Id' class='xod-children'>
                    <li class='xod-load-children'/>
                  </ul>
              </div>";
  } else {
    /*$mresult = xod_render($CoTable,$Id,$RenderContext,$RenderDepth);
    $chcls = "class='xod-children'";
    $children = "<div $chcls>".implode("</li><li $chcls>",$mresult)."</li>";
    $table .= "<div class='xod-children' id='Children$Id'
                  valign='top' rowspan='2'>
                  <ul id='Ul$Id' class='xod-children'>
                    $children
                  </ul>
              </td>";*/
  }
  $table .= "</li>";
  if (strlen($Text) > 0)
    $table .= "<li id='Text$Id' valign='top'
                  $onModifyElement>$Text</li>";
  $table .= "</ul>";

  return $table;
}

function xod_render($CoTable,$Key,$RenderContext=array(),$RenderDepth=1) {
  $result = array();
  $index = 0;
  if (!array_key_exists($Key,$CoTable))
    return $result;
  foreach ($CoTable[$Key] as $Id => $Child) {
    if ($Child["Kind"] === "element") {
      $index++;
      $ID = $Child["ID"];
      $attributes = array();
      $childNodes = array();
      if (array_key_exists($ID,$CoTable)) {
        $attributes = xmldb_cot_attributes($CoTable,$ID);
        $childNodes = xmldb_cot_childNodes($CoTable,$ID);
      }
      $tagName = $Child["Name"];
      if (array_key_exists($tagName,$RenderContext))
        $result[$ID] = $RenderContext[$tagName]($CoTable,$RenderContext,
                          $RenderDepth-1,$Child,$attributes,$childNodes);
      else
        $result[$ID] = xod_render_context($CoTable,$RenderContext,
                          $RenderDepth-1,$Child,$attributes,$childNodes);
    }
  }
  return $result;
}

function xod_close_element_editor($replyXML) {
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $editingTarget = "Editor";
  $forEditing = "&#160;";

  $targets = array("Editor-Title","Editor-Body");
  $payloads = array($editingTarget,$forEditing);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_save_changes($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $responses = $replyXML->getElementsByTagName("response");
  $Names = array();
  $Values = array();
  $Ids = array();
  foreach ($responses as $response) {
    $code = $response->getElementsByTagName("code")->item(0);
    $payload = $response->getElementsByTagName("payload")->item(0);
    $at_code = split("@",$code->nodeValue);
    if (sizeof($at_code) != 2)
      continue;
    switch ($at_code[0]) {
    case "Name":
      $Names[$at_code[1]] = $payload->nodeValue;
      break;
    case "Value":
      $Values[$at_code[1]] = $payload->nodeValue;
      break;
    }
    array_push($Ids,$at_code[1]);
  }
  $Ids = array_unique($Ids);

  $nodes = xmldb_getNodesOfSet($Connection, $Ids);
  foreach ($nodes as $Id => $node) {
    $save = false;
    $name = $node["Name"];
    $value = $node["Value"];
    if (array_key_exists($Id,$Names)) {
      if ($node["Name"] !== $Names[$Id]) {
        $save = true;
        $name = $Names[$Id];
      }
    }
    if (array_key_exists($Id,$Values)) {
      if ($node["Value"] !== $Values[$Id]) {
        $save = true;
        $value = $Values[$Id];
      }
    }
    if ($save) {
      xmldb_setElementNV($Connection,$Id,$name,$value);
    }
  }

  $node = xmldb_getElementById($Connection,$target);

  $tagNameTarget = "TN$target";
  $tagName = xod_translate_for_display($node["Name"]);
  $textTarget = "Text$target";
  $text = xod_translate_for_display($node["Value"]);

  $targets = array($tagNameTarget,$textTarget);
  $payloads = array($tagName,$text);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_save_targets_ajax_coding($Kind,$Id) {
  return "'+xmlencode(document.getElementById('$Kind$Id').value)+'";
}

function xod_on_xxx_build_ajax($event,$target,$code,$payload) {
  return "$event=\"".arpg_build_ajax($target,$code,$payload)."\"";
}
function xod_onclick_edit($code,$payload) {
  return xod_on_xxx_build_ajax("onclick","xmldb_editor.php",$code,$payload);
}

function xod_insert_fragment_values($Fragment,$Values) {
  foreach ($Values as $Name => $Value)
    $Fragment = preg_replace("/\%$Name\%/",$Value,$Fragment);
  return $Fragment;
}

function xod_build_default_text_editor($target) {
  $Connection = xmldb_create_connection();
  $node = xmldb_getElementById($Connection,$target);
  $attributes = xmldb_attributes($Connection,$node);

  $element_fragment = file_get_contents("modify_element.xmlf");
  $attr_fragment = file_get_contents("modify_attribute.xmlf");

  $save_codes = array("SaveChanges","Name@$target","Value@$target");
  $save_targets = array($target,
    xod_save_targets_ajax_coding("Name",$target),
    xod_save_targets_ajax_coding("Value",$target));

  $attribute_rows = "";
  foreach ($attributes as $aid => $attribute) {
    array_push($save_codes,"Name@$aid");
    array_push($save_targets,
      xod_save_targets_ajax_coding("Name",$aid));
    array_push($save_codes,"Value@$aid");
    array_push($save_targets,
      xod_save_targets_ajax_coding("Value",$aid));

    $rm_code = array("RemoveAttribute","Who");
    $rm_payl = array($target,$aid);

    $attr_vals = array(
      "attrId"=>$aid,
      "attrName"=>xod_translate_for_display($attribute["Name"]),
      "attrValue"=>xod_translate_for_display($attribute["Value"]),
      "remAttribute"=>xod_onclick_edit($rm_code,$rm_payl));

    $attribute_rows .= xod_insert_fragment_values($attr_fragment,$attr_vals);
  }

  $add_attr_code = array("AddAttribute","Name","Value");
  $add_attr_payl = array($target,
            xod_save_targets_ajax_coding("NameNew",$target),
            xod_save_targets_ajax_coding("ValueNew",$target));

  $elt_vals = array(
    "target"=>$target,
    "tagName"=>xod_translate_for_display($node["Name"]),
    "text"=>xod_translate_for_display($node["Value"]),
    "attributes"=>$attribute_rows,
    "moveUp"=>xod_onclick_edit("MoveUpOne",$target),
    "moveDown"=>xod_onclick_edit("MoveDownOne",$target),
    "moveTop"=>xod_onclick_edit("MoveToTop",$target),
    "moveBottom"=>xod_onclick_edit("MoveToBottom",$target),
    "appendChild"=>xod_onclick_edit("AppendChild",$target),
    "removeDangerously"=>xod_onclick_edit("RemoveDangerously",$target),
    "moveUp"=>xod_onclick_edit("MoveUpOne",$target),
    "onSaveChanges"=>xod_onclick_edit($save_codes,$save_targets),
    "onCloseTextEditor"=>xod_onclick_edit("CloseTextEditor",$target),
    "addAttribute"=>xod_onclick_edit($add_attr_code,$add_attr_payl));

  return xod_insert_fragment_values($element_fragment,$elt_vals);
}

function xod_add_remove_attribute($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;
  $code = $replyXML->getElementById("Code0")->firstChild->nodeValue;

  if ("AddAttribute" === $code) {
    $name = $replyXML->getElementById("Payload1")->firstChild->nodeValue;
    $value = $replyXML->getElementById("Payload2")->firstChild->nodeValue;
    xmldb_insert_attribute($Connection,$target,$name,$value);
  } else {
    $who = $replyXML->getElementById("Payload1")->firstChild->nodeValue;
    xmldb_removeAttributeById($Connection,$who);
  }

  $editorBody = xod_build_default_text_editor($target);

  $targets = array("Editor-Body");
  $payloads = array($editorBody);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_modify_element($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $node = xmldb_getElementById($Connection,$target);
  $attributes = xmldb_attributes($Connection,$node);

  $editingTarget = "Edit Element: #<a href='#Element$target'>$target</a>";

  $forEditing = xod_build_default_text_editor($target);

  $targets = array("Editor-Title","Editor-Body");
  $payloads = array($editingTarget,$forEditing);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_load_children($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $CoTable = xmldb_child_table_of_document($Connection);

  $mresult = xod_render($CoTable,$target);
  $items = "";
  foreach ($mresult as $key => $item) {
    $items .= "<li class='xod-child-item' id=\"ElementNode-$key\">$item</li>";
  }
  $result = "<ul class='xod-children' id='ENL$target'
                name='ElementNodeList' >
              $items
            </ul>";

  $toggleChildren = "onclick=\"toggleVisibility('ENL$target','none','block');
                            toggleMinimizeButton('MB$target','ENL$target');\"";

  $MBContent = "<div id='MB$target' $toggleChildren>+</div>";

  $targets = array("Children$target","MBCtr$target","@Evaluate");
  $payloads = array($result,$MBContent,"loadAllListsForDragDrop();");
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_reorder_list($replyXML) {

  $targets = array("Log");
  $payloads = array(xod_translate_for_display($replyXML->saveXML()));
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_append_child($replyXML) {
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $time = time();

  $targets = array("@AppendChild@Ul$target",
                    "@Evaluate");
  $payloads = array("<li id='Id$time'>".xod_translate_for_display($target)."</li>",
                    "loadAllListsForDragDrop();");
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_initialize($replyXML) {
  $Connection = xmldb_create_connection();
  $CoTable = xmldb_child_table_of_document($Connection);

  $cokeys = array_keys($CoTable);
  sort($cokeys);
  $mresult = xod_render($CoTable,$cokeys[0]);
  $result = implode("",$mresult);

  $targets = array("Display");
  $payloads = array($result);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_respond() {
  $reply = $_GET["Message"];
  $reply = str_replace("\\\"",'"',$reply);
  $replyXML = new DOMDocument();
  $replyXML->loadXML($reply);

  $targets = array();
  $payloads = array();

  foreach ($replyXML->getElementsByTagname("code") as $code) {
    $lres = array("Targets"=>array("Ajax"),
                  "Payloads"=>array("Unknown Code&#8658;".$code->nodeValue));

    switch ($code->nodeValue) {
    case "Initialize":
      $lres = xod_initialize($replyXML);
      break;
    case "LoadChildren":
      $lres = xod_load_children($replyXML);
      break;
    case "ModifyElement":
      $lres = xod_modify_element($replyXML);
      break;
    case "SaveChanges":
      $lres = xod_save_changes($replyXML);
      break;
    case "EditText":
      $lres = xod_text_editor($replyXML);
      break;
    case "CloseTextEditor":
      $lres = xod_close_element_editor($replyXML);
      break;
    case "AddAttribute":
    case "RemoveAttribute":
      $lres = xod_add_remove_attribute($replyXML);
      break;
    case "ReorderAndRechild":
      $lres = xod_reorder_list($replyXML);
    case "AppendChild":
      $lres = xod_append_child($replyXML);
    default: break;
    }

    foreach ($lres["Targets"] as $target)
      array_push($targets,$target);
    foreach ($lres["Payloads"] as $payload)
      array_push($payloads,$payload);
    // we only really care about code0
    break;
  }
  return arpg_build_responses($targets,$payloads);
}

echo xod_respond();

?>