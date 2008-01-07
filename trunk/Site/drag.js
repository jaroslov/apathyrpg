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
dragObj.zIndex = 10000;

function dragStart(event, id) {

  var el;
  var x, y;

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

  dragObj.elNode.style.left = (dragObj.elStartLeft + x - dragObj.cursorStartX) + "px";
  dragObj.elNode.style.top  = (dragObj.elStartTop  + y - dragObj.cursorStartY) + "px";

  event.preventDefault();
}

function dragStop(event) {

  // Stop capturing mousemove and mouseup events.

  document.removeEventListener("mousemove", dragGo,   true);
  document.removeEventListener("mouseup",   dragStop, true);
}
