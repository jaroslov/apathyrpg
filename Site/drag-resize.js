/***********************************************************************
* Generic Drag Demo                                                    *
*                                                                      *
* Copyright 2001 by Mike Hall                                          *
* Please see http://www.brainjar.com for terms of use.                 *
************************************************************************/

/*

  Modified by Jacob Smith (c) 2008. All rights are reserved to Mike Hall.
  Incorporated into ApathyRPG on Janurary 6, 2008.
  This file is not under GPL or any F/OSS copyright variant. All rights
  are reserved by Mike Hall (see above).

*/

var dragObj = new Object();
dragObj.active = false;
dragObj.zIndex = 10000;

var resizeObj = new Object();
resizeObj.active = false;
resizeObj.zIndex = 10000;

function resizeStart(event, id, body_id) {
  var el;
  var x, y;

  if (dragObj.active)
    return;

  // If an element id was given, find it. Otherwise use the element being
  // clicked on.

  if (id) {
    resizeObj.elNode = document.getElementById(id);
    resizeObj.elBodyNode = document.getElementById(body_id);
  } else {
    resizeObj.elNode = event.target;

    // If this is a text node, use its parent element.

    if (resizeObj.elNode.nodeType == 3)
      resizeObj.elNode = resizeObj.elNode.parentNode;
  }

  // Get cursor position with respect to the element.

  x = event.clientX;
  y = event.clientY;

  // Save starting positions of cursor and element.

  resizeObj.cursorStartX = x;
  resizeObj.cursorStartY = y;
  resizeObj.elStartLeft  = parseInt(resizeObj.elNode.style.left, 10);
  resizeObj.elStartTop   = parseInt(resizeObj.elNode.style.top,  10);

  resizeObj.elStartWidth = resizeObj.elNode.clientWidth;
  resizeObj.elStartHeight = resizeObj.elNode.clientHeight;

  if (isNaN(resizeObj.elStartLeft)) resizeObj.elStartLeft = 0;
  if (isNaN(resizeObj.elStartTop))  resizeObj.elStartTop  = 0;

  // Update element's z-index.

  resizeObj.elNode.style.zIndex = ++resizeObj.zIndex;

  // Capture mousemove and mouseup events on the page.

  if (event.target.tagName == "textarea")
    return;

  document.addEventListener("mousemove", resizeGo,   true);
  document.addEventListener("mouseup",   resizeStop, true);
  event.preventDefault();
}

function resizeGo(event) {

  var x, y, diff_x, diff_y;

  // Get cursor position with respect to the page.

  x = event.clientX;
  y = event.clientY;

  // Move drag element by the same amount the cursor has moved.

  diff_x = x-resizeObj.cursorStartX;
  diff_y = y-resizeObj.cursorStartY;

  resizeObj.elNode.style.width = (resizeObj.elStartWidth + diff_x) + "px";
  //resizeObj.elNode.style.height = (resizeObj.elStartHeight + diff_y) + "px";

  event.preventDefault();
}

function resizeStop(event) {

  // Stop capturing mousemove and mouseup events.

  document.removeEventListener("mousemove", resizeGo,   true);
  document.removeEventListener("mouseup",   resizeStop, true);
}

function dragStart(event, id) {

  var el;
  var x, y;

  dragObj.active = true;

  // If an element id was given, find it. Otherwise use the element being
  // clicked on.

  if (id)
    dragObj.elNode = document.getElementById(id);
  else {
    dragObj.elNode = event.target;

    // If this is a text node, use its parent element.

    if (dragObj.elNode.nodeType == 3)
      dragObj.elNode = dragObj.elNode.parentNode;
  }

  // Get cursor position with respect to the page.

  x = event.clientX + window.scrollX;
  y = event.clientY + window.scrollY;

  // Save starting positions of cursor and element.

  dragObj.cursorStartX = x;
  dragObj.cursorStartY = y;
  dragObj.elStartLeft  = parseInt(dragObj.elNode.style.left, 10);
  dragObj.elStartTop   = parseInt(dragObj.elNode.style.top,  10);

  if (isNaN(dragObj.elStartLeft)) dragObj.elStartLeft = 0;
  if (isNaN(dragObj.elStartTop))  dragObj.elStartTop  = 0;

  // Update element's z-index.

  dragObj.elNode.style.zIndex = ++dragObj.zIndex;

  // update opacity
  dragObj.elOpacity = dragObj.elNode.style.opacity;
  dragObj.elNode.style.opacity = .65;

  // Capture mousemove and mouseup events on the page.

  document.addEventListener("mousemove", dragGo,   true);
  document.addEventListener("mouseup",   dragStop, true);
  event.preventDefault();
}

function dragGo(event) {

  var x, y;

  // Get cursor position with respect to the page.

  x = event.clientX + window.scrollX;
  y = event.clientY + window.scrollY;

  // Move drag element by the same amount the cursor has moved.

  dragObj.elNode.style.width = dragObj.elNode.clientWidth+"px";
  dragObj.elNode.style.left = (dragObj.elStartLeft + x - dragObj.cursorStartX) + "px";
  dragObj.elNode.style.top  = (dragObj.elStartTop  + y - dragObj.cursorStartY) + "px";

  event.preventDefault();
}

function dragStop(event) {

  dragObj.active = false;

  dragObj.elNode.style.opacity = dragObj.elOpacity;

  // Stop capturing mousemove and mouseup events.

  document.removeEventListener("mousemove", dragGo,   true);
  document.removeEventListener("mouseup",   dragStop, true);
}
