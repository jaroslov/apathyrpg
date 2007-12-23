<?php

function get_apathy_dom($name) {
  if ($name === null)
    $name = "Apathy.xml";
  $ApathyXml = simplexml_load_file($name);
  $ApathyDom = dom_import_simplexml($ApathyXml);
  return $ApathyDom;
}

function get_apathy_xml($apathydom) {
  return $apathydom->ownerDocument;
}

function translate_child_text($node) {
  $text = "";
  foreach ($node->childNodes as $child)
    $text .= translate_text($child);
  return $text;
}

function translate_text($node) {
  if ("Apathy" === $node->tagName)
    return "{Apathy}";
  if ("text" === $node->tagName)
    return translate_child_text($node);
  else if ("C" === $node->tagName)
    return "{C}";
  else if ("plusminus" === $node->tagName)
    return "&#177;";
  else if ("and" === $node->tagName)
    return "&";
  else if ("dollar" === $node->tagName)
    return "$";
  else if ("percent" === $node->tagName)
    return "%";
  else if ("rightarrow" === $node->tagName)
    return "&#8594";
  else if ("ldquo" === $node->tagName)
    return "&ldquo;";
  else if ("lsquo" === $node->tagName)
    return "&lsquo;";
  else if ("rdquo" === $node->tagName)
    return "&rdquo;";
  else if ("rsquo" === $node->tagName)
    return "&rsquo;";
  else if ("mdash" === $node->tagName)
    return "&mdash;";
  else if ("ndash" === $node->tagName)
    return "&ndash;";
  else if ("ouml" === $node->tagName)
    return "&ouml;";
  else if ("oslash" === $node->tagName)
    return "&#248;";
  else if ("trademark" === $node->tagName)
    return "&#8482;";
  else if ("times" === $node->tagName)
    return "*";
  else if ("Sum" === $node->tagName)
    return "&#8721;";
  else if ("roll" === $node->tagName)
    return "{roll ".translate_child_text($node)."}";
  else if ("raw" === $node->tagName)
    return "[".translate_child_text($node)."]";
  else if ("rOff" === $node->tagName)
    return "{rOff ".translate_child_text($node)."}";
  else if ("num" === $node->tagName)
    return "{num ".translate_child_text($node)."}";
  else if ("face" === $node->tagName)
    return "{face ".translate_child_text($node)."}";
  else if ("bOff" === $node->tagName)
    return "{bOff ".translate_child_text($node)."}";
  else if ("bns" === $node->tagName)
    return "{bns ".translate_child_text($node)."}";
  else if ("mul" === $node->tagName)
    return "{mul ".translate_child_text($node)."}";
  else if ("kind" === $node->tagName)
    return "{kind ".translate_child_text($node)."}";
  else if ("notappl" === $node->tagName)
    return "{n/a}";
  else if ("define" === $node->tagName)
    return "{def".translate_text($node->childNodes)."}";
  else if ("crushing" === $node->tagName)
    return "crushing";
  else if ("math" === $node->tagName)
    return "{math ".translate_child_text($node)."}";
  else if ("mrow" === $node->tagName)
    return "{".translate_child_text($node)."}";
  else if ("mi" === $node->tagName)
    return "{".translate_child_text($node)."}";
  else if ("mo" === $node->tagName)
    return translate_child_text($node);
  else if ("mn" === $node->tagName)
    return translate_child_text($node);
  else if ("msup" === $node->tagName)
    return "{msup ".translate_child_text($node)."}";
  else if ("munderover" === $node->tagName)
    return "{munderover ".translate_child_text($node)."}";
  else if ("mfrac" === $node->tagName)
    return "{mfrac ".translate_child_text($node)."}";
  else if ("mstyle" === $node->tagName)
    return "{mstyle ".translate_child_text($node)."}";
  else if ("footnote" === $node->tagName)
    return "{footnote}";
  else if ($node->nodeType === 3)
    return $node->nodeValue;
  else if ("numbered-list" === $node->tagName) {
    return "\n{numbered-list }\n";
  } else if ("itemized-list" === $node->tagName) {
    return "\n{itemized-list }\n";
  } else if ("description-list" === $node->tagName) {
    return "\n{description-list }\n";
  } else
    return "{nodeType ".$node->nodevalue."@".$node->tagName.":".(string) $node->nodeType."}";
}

function message_to_datum($apathy,$message) {
  $atparts = explode("@",$message);
  $category_datum = $atparts[1];
  $bangparts = explode("!",$category_datum);
  $category_id = $bangparts[0];
  $datum_id = $bangparts[1];
  return $apathy->getElementById($datum_id);
}

function get_name_of_datum($datum) {
  foreach ($datum->childNodes as $field)
    if ($field->tagName === "field")
      if (false !== $field->hasAttribute("title"))
        return translate_child_text($field);
  return "No name.";
}

function get_title_of_datum($datum) {
  foreach ($datum->childNodes as $field)
    if ($field->tagName === "field")
      if (false !== $field->hasAttribute("title"))
        return translate_child_text($field);
  return "No title.";
}

function get_description_of_datum($datum) {
  foreach ($datum->childNodes as $field)
    if ($field->tagName === "field")
      if (false !== $field->hasAttribute("description"))
        return translate_child_text($field);
  return "No description.";
}

function get_table_of_datum($datum) {
  $tables = array();
  foreach ($datum->childNodes as $field)
    if ($field->tagName === "field")
      if (false !== $field->hasAttribute("table"))
        array_push($tables,translate_child_text($field));
  return $tables;
}

function format_datum($title,$tables,$description) {
  $result = "<table>";
  $rows = sizeof($tables)+1;
  $result .= "<tr><td></td><td>Aspects</td><td>Description</td></tr>";
  $result .= "<tr><td>Title</td><td>".$title."</td>"
  $result .= "<td rowspan='".$rows."'>".$description."</td></tr>";
  foreach ($tables as $table)
    $result .= "<tr></tr>";
  $result .= "</table>";
  return $result;
}

?>