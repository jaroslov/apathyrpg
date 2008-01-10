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
  // build attributes
  $Attrs = "<tr>";
  $attr_class = "class='xod-attr-val'";
  foreach ($attributes as $Name => $Value) {
    $Attrs .= "<td class='xod-attr-name'>$Name</td>
                <td $attr_class>$Value</td>";
    $attr_class = "class='xod-attr-val'";
  }
  $Attrs .= "</tr>";

  $rowspan = sizeof($attributes)+1;
  if (strlen($nodeValue)>0)
    $Text = xod_translate_for_display($nodeValue);

  $onShowChildren = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php",
      array("LoadChildren"),
      array($Id))
    .";\"";

  $toggleChildren = "onclick=\"toggleVisibility('Ul$Id','none','block');
                              toggleMinimizeButton('MB$Id','Ul$Id');\"";

  $NC = sizeof($childNodes);

  $onModifyElement = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php",
      array("ModifyElement"),
      array($Id))
    .";\"";

  if ($RenderDepth==0)
    $MMBut = "&#8211;";
  else
    $MMBut = "+";

  $table  = "<table id='Element$Id'"
              // TODO: implement dragStartDup so the user can
              // "drag" to move
              //." onmousedown=\"dragStartDup(event, 'Element$Id');\""
              ." class='xod-element'>";
  $table .= "<tbody>";
  $table .= "<tr class='xod-tag-text-group'>";
  $table .= "<td class='xod-mm-button'
                  id='MB$Id' valign='top'
                  $toggleChildren>$MMBut</td>";
  $table .= "<td class='xod-tagname'
                  id='TN$Id' valign='top'
                  $onModifyElement>$tagName</td>";
  $child_num = "Child";
  if ($RenderDepth == 0) {
    if ($NC > 1) $child_num .= "ren";
    if ($NC > 0)
      $table .= "<td class='xod-children'
                    id='Children$Id'
                     valign='top'
                    rowspan='2'>
                    <ul id='Children$Id' class='xod-children'>
                      <li class='xod-load-children'
                          $onShowChildren>Load $NC $child_num</li>
                    </ul>
                </td>";
  } else {
    $mresult = xod_render($CoTable,$Id,$RenderContext,$RenderDepth);
    $chcls = "class='xod-children'";
    $children = "<li $chcls>".implode("</li><li $chcls>",$mresult)."</li>";
    $table .= "<td class='xod-children' id='Children$Id'
                  valign='top' rowspan='2'>
                  <ul id='Ul$Id' class='xod-children'>
                    $children
                  </ul>
              </td>";
  }
  $table .= "</tr>";
  $table .= "<tr class='xod-tag-text-group'>";
  $table .= "<td class='xod-text' colspan='2'
                id='Text$Id'
                valign='top'
                $onModifyElement>$Text</td>";
  $table .= "</tr>";
  $table .= "</tbody>";
  $table .= "</table>";
  return $table;
}

function xod_render($CoTable,$Key,$RenderContext=array(),$RenderDepth=3) {
  $result = array();
  $index = 0;
  $number_children = sizeof($CoTable[$Key]);
  foreach ($CoTable[$Key] as $Id => $Child) {
    if ($Child["Kind"] === "element") {
      $index++;
      $ID = $Child["ID"];
      $attributes = xmldb_cot_attributes($CoTable,$ID);
      $childNodes = xmldb_cot_childNodes($CoTable,$ID);
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

function xod_text_edit_menu($node) {
  $text_id = $node;
  $close = "<div class='Edit-TD' onclick=\""
            .arpg_build_ajax("xmldb_editor.php","CloseTextEditor",$text_id)
            ."\">Close</div>";
  $save = "<div class='Edit-TD' onclick=\""
          .arpg_build_ajax("xmldb_editor.php",array("SaveChanges","What"),
              array($text_id,
              "'+xmlencode(document.getElementById('TA$text_id').value)+'"))
          ."\">Save Changes</div>";
  $spacer = "<div class='Edit-TD' style='width:1000em;padding:0;min-width:0;border:0;'></div>";
  return "<div class='Edit-Controls'>$structure$save$spacer$close</div>";
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
  $Msg = "<br/>".sizeof($nodes)."<br/>";
  foreach ($nodes as $Id => $node) {
    if (array_key_exists($Id,$Names)) {
      if ($node["Name"] !== $Names[$Id])
        $Msg .= xod_translate_for_display($node["ID"].'"!=="')."<br/>";
      else
        $Msg .= xod_translate_for_display($node["ID"].'"==="')."<br/>";
    }
    if (array_key_exists($Id,$Values)) {
      if ($node["Value"] !== $Values[$Id])
        $Msg .= xod_translate_for_display($node["ID"].'"!=="')."<br/>";
      else
        $Msg .= xod_translate_for_display($node["ID"].'"==="')."<br/>";
    }
  }

  $msg = "Saving #".implode(" #",$Ids)."$Msg";

  $targets = array("Ajax");
  $payloads = array($msg);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_save_targets_ajax_coding($Kind,$Id) {
  return "'+xmlencode(document.getElementById('$Kind$Id').value)+'";
}

function xod_modify_attribute($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;
  $name = $replyXML->getElementById("Payload1")->firstChild->nodeValue;
  $value = $replyXML->getElementById("Payload2")->firstChild->nodeValue;

  //$node = xmldb_getElementById($Connection,$target);
  //$attributes = xmldb_attributes($Connection,$node);

  $msg = "$target: $name &#8658; $value";

  $targets = array("Ajax");
  $payloads = array($msg);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_modify_element($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $node = xmldb_getElementById($Connection,$target);
  $attributes = xmldb_attributes($Connection,$node);

  $editingTarget = "Edit Element: #<a href='#Id$target'>$target</a>";

  $tagName = xod_translate_for_display($node["Name"]);
  $text = xod_translate_for_display($node["Value"]);

  $save_codes = array("SaveChanges","Name@$target","Value@$target");
  $save_targets = array($target,
    xod_save_targets_ajax_coding("Name",$target),
    xod_save_targets_ajax_coding("Value",$target));
  foreach ($attributes as $aid => $attribute) {
    array_push($save_codes,"Name@$aid");
    array_push($save_targets,
      xod_save_targets_ajax_coding("Name",$aid));
    array_push($save_codes,"Value@$aid");
    array_push($save_targets,
      xod_save_targets_ajax_coding("Value",$aid));
  }

  $close = "<div class='Edit-TD' onclick=\""
            .arpg_build_ajax("xmldb_editor.php","CloseTextEditor",$target)
            ."\">Close</div>";
  $save = "<div class='Edit-TD' onclick=\""
          .arpg_build_ajax("xmldb_editor.php",$save_codes,$save_targets)
          ."\">Save Changes</div>";
  $moveUp = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php","MoveUpOne",$$target)."\"";
  $moveDown = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php","MoveDownOne",$$target)."\"";
  $moveTop = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php","MoveToTop",$$target)."\"";
  $moveBottom = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php","MoveToBottom",$$target)."\"";
  $appendChild = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php","AppendChild",$$target)."\"";
  $removeDangerously = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php","RemoveDangerously",$$target)."\"";
  $structure = "<div class='Edit-TD'>
                  <ul class='MainMenu'>
                    <li>Structure...
                      <ul class='Menu'>
                        <li>Move...
                          <ul class='Menu'>
                            <li $moveUp>Up one</li>
                            <li $moveDown>Down one</li>
                            <li $moveTop>To top</li>
                            <li $moveBottom>To bottom</li>
                          </ul>
                        </li>
                        <li $appendChild>Append Child</li>
                        <li $removeDangerously>Remove</li>
                      </ul>
                    </li>
                  </ul>
                 </div>";
  $spacer = "<div class='Edit-TD-Spacer'></div>";
  $menu = "<div class='Edit-Controls'>$structure$save$spacer$close</div>";

  $forEditing = "";
  $forEditing .= "$menu";
  $forEditing .= "<table class='xod-elt-table'>";
  $forEditing .= "<thead>
                    <th class='title' colspan='3'>
                      Name: <textarea class='xod-attr-ta'
                        id='Name$target'>$tagName</textarea>
                    </th>
                  </thead>";
  $forEditing .= "<thead>
                    <th class='heading'>Action</th>
                    <th class='heading'>Name</th>
                    <th class='heading'>Value</th>
                  </thead>";
  $forEditing .= "<tbody>";
  $addAttribute = "onclick=\""
        .arpg_build_ajax("xmldb_editor.php",
          array("AddAttribute","Name","Value"),
          array($target,
            xod_save_targets_ajax_coding("NameNew",$target),
            xod_save_targets_ajax_coding("ValueNew",$target)))."\"";
  foreach ($attributes as $aid => $attribute) {
    $remAttribute = "onclick=\""
          .arpg_build_ajax("xmldb_editor.php",
            array("RemoveAttribute","Who"),
            array($target,$aid))."\"";
    $name = xod_translate_for_display($attribute["Name"]);
    $value = xod_translate_for_display($attribute["Value"]);
    $forEditing .= "<tr>
                      <td class='xod-attr-name'
                        $remAttribute>
                        <div class='xod-button-emu'>Remove</div>
                      </td>
                      <td class='xod-attr-name'>
                        <textarea class='xod-attr-ta'
                          id='Name$aid'>$name</textarea>
                      </td>
                      <td class='xod-attr-val'>
                        <textarea class='xod-attr-ta'
                          id='Value$aid'>$value</textarea>
                      </td>
                    </tr>";
  }
  $forEditing .= "<tr>
                    <td class='xod-attr-name'
                      $addAttribute>
                      <div class='xod-button-emu'>Add</div>
                    </td>
                    <td class='xod-attr-name'>
                      <textarea class='xod-attr-ta'
                        id='NameNew$target'></textarea>
                    </td>
                    <td class='xod-attr-val'>
                      <textarea class='xod-attr-ta'
                        id='ValueNew$target'></textarea>
                    </td>
                  </tr>";
  $forEditing .= "</tbody>";
  $forEditing .= "</table>";
  $forEditing .= "<textarea id='Value$target'>$text</textarea>";

  $targets = array("Editor-Title","Editor-Body");
  $payloads = array($editingTarget,$forEditing);
  return array("Targets"=>$targets,"Payloads"=>$payloads);
}

function xod_load_children($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $CoTable = xmldb_child_table_of_document($Connection);

  $mresult = xod_render($CoTable,$target);
  $result = "<ul class='xod-children' id='Ul$target'>
              <li>".implode("</li><li>",$mresult)."</li>
            </ul>";

  $targets = array("Children$target","MB$target");
  $payloads = array($result,"+");
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
      $lres = xod_modify_attribute($replyXML);
      break;
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