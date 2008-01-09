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

function xod_render_context($CoTable,$RenderContext,$node,$attributes,$childNodes) {
  $Id = $node["ID"];
  // build a table
  //  tag-name
  //    attr val    descr
  //    attr val
  //    children...
  $tagName = $node["Name"];
  $nodeValue = $node["Value"];
  // build attributes
  $Attrs = "";
  $attr_class = "class='xod-attr-val'";
  foreach ($attributes as $Name => $Value) {
    $Attrs .= "<tr><td class='xod-attr'>$Name</td>
                <td $attr_class>$Value</td></tr>";
    $attr_class = "class='xod-attr-val'";
  }

  $rowspan = sizeof($attributes)+1;
  if (strlen($nodeValue)>0)
    $Text = xod_translate_for_display($nodeValue);
  else
    $Text = "<em class='xod-no-text'>No Text.</em>";

  $onTextEdit = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php",
      array("EditText"),
      array($Id))
    .";\"";

  $onShowChildren = "onclick=\""
    .arpg_build_ajax("xmldb_editor.php",
      array("LoadChildren"),
      array($Id))
    .";\"";

  $toggleChildren = "onclick=\"toggleVisibility('Ul$Id','none','block');
                              toggleMinimizeButton('MB$Id');\"";

  $NC = sizeof($childNodes);

  $table = "";
  $table .= "<table class='xod-table' id='Id$Id'>";
  $table .= "<thead>
              <th id='MB$Id' $toggleChildren>--</th>
              <th $toggleChildren>$tagName</th>
              <th>Text</th>
            </thead>";
  $table .= "<tbody>
              $Attrs
              <tr class='xod-descr' $onTextEdit id='Text$Id'>
                <td colspan='3' class='xod-descr'>$Text</td>
              </tr>
            </tbody>";
  $table .= "</table>";
  $child_num = "Child";
  if ($NC > 1) $child_num .= "ren";
  if ($NC > 0)
    $table .= "<ul id='Children$Id' class='xod-children'>
                <li $onShowChildren>
                  Show $NC $child_num
                </li>
              </ul>";
  return $table;
}

function xod_render($CoTable,$Key,$RenderContext=array()) {
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
                          $Child,$attributes,$childNodes);
      else
        $result[$ID] = xod_render_context($CoTable,$RenderContext,
                          $Child,$attributes,$childNodes);
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

function xod_text_editor($replyXML) {
  $Connection = xmldb_create_connection();
  $target = $replyXML->getElementById("Payload0")->firstChild->nodeValue;

  $node = xmldb_getElementById($Connection, $target);
  $nodeValue = $node["Value"];
  $text = xod_translate_for_display($nodeValue);

  $menuBar = xod_text_edit_menu($node);
  $forEditing = "$menuBar<textarea id='TA$target'>$text</textarea>";

  $targets = array("Editor-Title","Editor-Body");
  $payloads = array("Editing #$target",$forEditing);
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

  $targets = array("Children$target");
  $payloads = array($result);
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
    $lres = array("Targets"=>array("Display"),
                  "Payloads"=>array("Unknown Code&#8658;".$code->nodeValue));

    switch ($code->nodeValue) {
    case "Initialize":
      $lres = xod_initialize($replyXML);
      break;
    case "LoadChildren":
      $lres = xod_load_children($replyXML);
      break;
    case "EditText":
      $lres = xod_text_editor($replyXML);
      break;
    default: break;
    }

    foreach ($lres["Targets"] as $target)
      array_push($targets,$target);
    foreach ($lres["Payloads"] as $payload)
      array_push($payloads,$payload);
  }
  return arpg_build_responses($targets,$payloads);
}

echo xod_respond();

?>