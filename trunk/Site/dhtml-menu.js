function arpg_hide_descendents(element) {
  for (var i=0; i<element.childNodes.length; i++) {
    child = element.childNodes[i];
    if (child.nodeType == 1 && child.tagName == "ul") {
      child.style.display = "none";
      arpg_hide_descendents(child);
    }
  }
}

function arpg_show_descendents(element) {
  for (var i=0; i<element.childNodes.length; i++) {
    child = element.childNodes[i];
    if (child.nodeType == 1)
      child.style.display = "block";
  }
}

function siblings(target) {
  var parent = target.parentNode;
  var result = new Array();
  for (var i=0; i<parent.childNodes.length; i++) {
    child = parent.childNodes[i];
    if (child != target)
      result.push(child);
  }
  return result;
}

function arpg_toggle_display(target) {
  // turn off all siblings
  if (target.tagName != "li") {
    var par = target;
    while (par.tagName != "li")
      par = par.parentNode;
    target = par;
  }
  sibs = siblings(target);
  for (var i=0; i<sibs.length; i++)
    arpg_hide_descendents(sibs[i]);
  for (var i=0; i<target.childNodes.length; i++) {
    child = target.childNodes[i];
    if (child.nodeType == 1 && child.tagName == "ul") {
      switch (child.style.display) {
      case "none":
        child.style.display = "block";
        arpg_show_descendents(child);
        break;
      case "block":
      default:
        child.style.display = "none";
        arpg_hide_descendents(child);
        break;
      }
    }
  }
}

function arpg_click(event) {
  // find parent ul
  arpg_toggle_display(event.target);
  event.stopPropagation();
}

function arpg_set_zindex_by_depth(element,index) {
  if (element.tagName == "li")
    element.style.position = "relative";
  else if (element.tagName == "ul")
    element.style.position = "absolute";
  element.style.zIndex = index;
  var len = element.childNodes.length;
  for (var i=0; i<element.childNodes.length; i++)
    if (element.childNodes[i].nodeType == 1)
      arpg_set_zindex_by_depth(element.childNodes[i],index+len-i);
}

function arpg_add_all_listeners(element) {
  element.addEventListener('click',arpg_click,false);
  for (var i=0; i<element.childNodes.length; i++)
    if (element.childNodes[i].nodeType == 1)
      arpg_add_all_listeners(element.childNodes[i]);
}

function arpg_register(element) {
  arpg_add_all_listeners(element);
  arpg_set_zindex_by_depth(element,1);
}

function argp_default(message) {
  alert(message);
}

function arpg_action(message,func) {
  if (func)
    func(message);
}
