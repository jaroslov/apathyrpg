function loadAllListsForDragDrop() {
  (function() {
  
  var Dom = YAHOO.util.Dom;
  var Event = YAHOO.util.Event;
  var DDM = YAHOO.util.DragDropMgr;
  
  // example should be tailored to each list
  // e.g. example$TargetId
  // this allows us to have a bunch of lists which are editable
  //////////////////////////////////////////////////////////////////////////////
  // example app
  //////////////////////////////////////////////////////////////////////////////
  YAHOO.example.DDApp = {
      init: function() {
  
          // we add ULs as DDTargets here
          // new YAHOO.util.DDTarget("ul-id");
          var ULs = document.getElementsByTagName("ul");
          for (var i=0; i<ULs.length; i++) {
            var ul_id = ULs[i].id;
            new YAHOO.util.DDTarget(ul_id);
            for (var j=0; j<ULs[i].childNodes.length; j++) {
              // we add the LIs of the DDTargets here
              var child = ULs[i].childNodes[j];
              if (child.nodeType == 1)
                if (child.tagName == "li")
                  new YAHOO.example.DDList(child.id);
            }
          }
  
          //Event.on("showButton", "click", this.showOrder);
          //Event.on("switchButton", "click", this.switchStyles);
      }
  };
  
  //////////////////////////////////////////////////////////////////////////////
  // custom drag and drop implementation
  //////////////////////////////////////////////////////////////////////////////
  
  YAHOO.example.DDList = function(id, sGroup, config) {
  
      YAHOO.example.DDList.superclass.constructor.call(this, id, sGroup, config);
  
      var el = this.getDragEl();
      Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent
  
      this.goingUp = false;
      this.lastY = 0;
  };
  
  YAHOO.extend(YAHOO.example.DDList, YAHOO.util.DDProxy, {
  
      startDrag: function(x, y) {
  
          // make the proxy look like the source element
          var dragEl = this.getDragEl();
          var clickEl = this.getEl();
          Dom.setStyle(clickEl, "visibility", "hidden");
  
          dragEl.innerHTML = clickEl.innerHTML;
  
          // modifies the style
          Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
          Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
          Dom.setStyle(dragEl, "border", "2px solid gray");
      },
  
      endDrag: function(e) {
  
          var srcEl = this.getEl();
          var proxy = this.getDragEl();
  
          // Show the proxy element and animate it to the src element's location
          Dom.setStyle(proxy, "visibility", "");
          var a = new YAHOO.util.Motion( 
              proxy, { 
                  points: { 
                      to: Dom.getXY(srcEl)
                  }
              }, 
              0.2, 
              YAHOO.util.Easing.easeOut 
          )
          var proxyid = proxy.id;
          var thisid = this.id;
  
          // Hide the proxy and show the source element when finished with the animation
          a.onComplete.subscribe(function() {
                  Dom.setStyle(proxyid, "visibility", "hidden");
                  Dom.setStyle(thisid, "visibility", "");
              });
          a.animate();
          // when we're done with it, clear the proxy so that
          // the unique-ids that it copies are available again
          proxy.innerHTML = "";
          // also, fire a re-ordering message
          ajaxFunction("xmldb_editor.php",
            "<reply>"
              +"<response id=\"Response0\">"
                +"<code id=\"Code0\">ReorderAndRechild</code>"
                +"<payload id=\"Payload0\">%target%</payload>"
              +"</response>"
            +"</reply>");
      },
  
      onDragDrop: function(e, id) {
  
          // If there is one drop interaction, the li was dropped either on the list,
          // or it was dropped on the current location of the source element.
          if (DDM.interactionInfo.drop.length === 1) {
  
              // The position of the cursor at the time of the drop (YAHOO.util.Point)
              var pt = DDM.interactionInfo.point; 
  
              // The region occupied by the source element at the time of the drop
              var region = DDM.interactionInfo.sourceRegion; 
  
              // Check to see if we are over the source element's location.  We will
              // append to the bottom of the list once we are sure it was a drop in
              // the negative space (the area of the list without any list items)
              if (!region.intersect(pt)) {
                  var destEl = Dom.get(id);
                  var destDD = DDM.getDDById(id);
                  destEl.appendChild(this.getEl());
                  destDD.isEmpty = false;
                  DDM.refreshCache();
              }
  
          }
      },
  
      onDrag: function(e) {
  
          // Keep track of the direction of the drag for use during onDragOver
          var y = Event.getPageY(e);
  
          if (y < this.lastY) {
              this.goingUp = true;
          } else if (y > this.lastY) {
              this.goingUp = false;
          }
  
          this.lastY = y;
      },
  
      onDragOver: function(e, id) {
      
          var srcEl = this.getEl();
          var destEl = Dom.get(id);
  
          // We are only concerned with list items, we ignore the dragover
          // notifications for the list.
          if (destEl.nodeName.toLowerCase() == "li") {
              var orig_p = srcEl.parentNode;
              var p = destEl.parentNode;
  
              if (this.goingUp) {
                  p.insertBefore(srcEl, destEl); // insert above
              } else {
                  p.insertBefore(srcEl, destEl.nextSibling); // insert below
              }
  
              DDM.refreshCache();
          }
      }
  });
  
  Event.onDOMReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);
  
  })();
}
function createHandleDraggableEditor() {
  YAHOO.example.DDOnTop = function(id, sGroup, config) {
      YAHOO.example.DDOnTop.superclass.constructor.apply(this, arguments);
  };

  YAHOO.example.DDResize = function(panelElId, handleElId, sGroup, config) {
      YAHOO.example.DDResize.superclass.constructor.apply(this, arguments);
      if (handleElId) {
          this.setHandleElId(handleElId);
      }
  };

  YAHOO.extend(YAHOO.example.DDOnTop, YAHOO.util.DD, {
      origZ: 0,
      origO: 1,
  
      startDrag: function(x, y) {
  
          var style = this.getEl().style;
  
          // store the original z-index
          this.origZ = style.zIndex;
          this.origO = style.opacity;
  
          // The z-index needs to be set very high so the element will indeed be on top
          style.zIndex = 10000;
          style.opacity = .65;
      },
  
      endDrag: function(e) {
          YAHOO.log(this.id + " endDrag", "info", "example");
  
          // restore the original z-index
          this.getEl().style.zIndex = this.origZ;
          this.getEl().style.opacity = 1;
      }
  });


  YAHOO.extend(YAHOO.example.DDResize, YAHOO.util.DragDrop, {
  
      onMouseDown: function(e) {
          var panel = this.getEl();
          this.startWidth = panel.offsetWidth;
          this.startHeight = panel.offsetHeight;
  
          this.startPos = [YAHOO.util.Event.getPageX(e),
                           YAHOO.util.Event.getPageY(e)];
      },
  
      onDrag: function(e) {
          var newPos = [YAHOO.util.Event.getPageX(e),
                        YAHOO.util.Event.getPageY(e)];
  
          var offsetX = newPos[0] - this.startPos[0];
          var offsetY = newPos[1] - this.startPos[1];
  
          var newWidth = Math.max(this.startWidth + offsetX, 10);
          var newHeight = Math.max(this.startHeight + offsetY, 10);
  
          var panel = this.getEl();
          panel.style.width = newWidth + "px";
          panel.style.height = newHeight + "px";
      }
  });
  
  (function() {
      var dd, dd2;
      YAHOO.util.Event.onDOMReady(function() {
          // put the resize handle and panel drag and drop instances into different
          // groups, because we don't want drag and drop interaction events between
          // the two of them.
          dd = new YAHOO.example.DDResize("Editor", "Editor-Handle", "panelresize");
          dd = new YAHOO.example.DDResize("Editor-Body", "Editor-Handle", "panelresize");
          dd2 = new YAHOO.example.DDOnTop("Editor", "paneldrag");
          dd2.setHandleElId("Editor-Title-Bar");
  
          // addInvalidHandleid will make it so a mousedown on the resize handle will 
          // not start a drag on the panel instance.  
          dd2.addInvalidHandleId("Editor-Handle");
      });
  })();

}
function toggleVisibility(Id,On,Off) {
  var who = document.getElementById(Id);
  if (who)
    if (who.style.display == On)
      who.style.display = Off;
    else
      who.style.display = On;
}
function toggleChildVisibility(Id,On,Off) {
  var who = document.getElementById(Id);
  for (var i=0; i<who.childNodes.length; i++)
    if (who.childNodes[i].nodeType == 1) {
      if (who.style.display == On)
        who.style.display = Off;
      else
        who.style.display = On;
    }
}
function toggleMinimizeButton(Id,IfId) {
  var If = document.getElementById(IfId)
  if (If) {
    var who = document.getElementById(Id);
    if (who.innerHTML != "+")
      who.innerHTML = "+";
    else
      who.innerHTML = "&#8211;";
  }
}
function initialLoad(who) {
  ajaxFunction(who,
    '<reply><response><code>Initialize</code></response></reply>');
}
function arpg_size(Id) {
  var who = document.getElementById(Id);
  return who.scrollWidth+":"+who.scrollHeight;
}
function realUnescape(string) {
  var str = string.replace(/\\/,"");
  //var str = string.replace(/\</,"&lt;");
  //var str = string.replace(/\>/,"&gt;");
  return str;
}
function xmlencode(str) {
  for (i = 0; i<str.length; i++) {
    str = str.replace('&', '{@and}');
    str = str.replace('<', '{@less}');
    str = str.replace('>', '{@greater}');
    str = str.replace("'", '{@apos}');
    str = str.replace('"', '{@quot}');
  }
  for (i = 0; i<str.length; i++) {
    str = str.replace('{@and}', '&amp;');
    str = str.replace('{@less}', '&lt;');
    str = str.replace('{@greater}', '&gt;');
    str = str.replace('{@apos}', '&apos;');
    str = str.replace('{@quot}', '&quot;');
  }
  return str;
}
function urlencode(str) {
  str = escape(str);
  // javascript is megagay
  for (i = 0; i<str.length; i++) {
    // oy... don't enable
    //str = str.replace('%20', ' ');
    str = str.replace('+', '%2B');
    str = str.replace('*', '%2A');
    str = str.replace('/', '%2F');
    str = str.replace('@', '%40');
    str = str.replace('&', '%26');
  }
  return str;
}
function ajaxFunction(Responder,Message) {
  xmlHttp=new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (4 == xmlHttp.readyState) {
      var xmlR = xmlHttp.responseText;
      var domp = new DOMParser();
      var responseXml = domp.parseFromString(xmlR, "text/xml");
      var replies = responseXml.getElementsByTagName("response");
      var logresponse = document.getElementById('LogResponse');
      var log = document.getElementById('Log');
      if (log)
        log.innerHTML = realUnescape(xmlR);
      if (logresponse)
        logresponse.innerHTML = realUnescape(xmlR);
      for (i = 0; i<replies.length; i++) {
        replies[i].normalize();
        var target = replies[i].getElementsByTagName("target")[0].firstChild.nodeValue;
        var payload = replies[i].getElementsByTagName("payload")[0];
        var range = document.createRange();
        var target = replies[i].getElementsByTagName("target")[0].firstChild.nodeValue;
        var payload = replies[i].getElementsByTagName("payload")[0];
        if ("@" == target[0]) {
          codes = target.split("@");
          code = codes[1];
          if (code == "Focus") {
            document.getElementById(codes[2]).focus();
          } else if (code == "Title") {
            document.title = payload.firstChild.nodeValue;
          } else if (code == "Evaluate") {
            eval(payload.firstChild.nodeValue);
          } else if (code == "AppendChild") {
            var who = document.getElementById(codes[2])
            if (who) {
              who.innerHTML += payload.firstChild.nodeValue
            }
          }
        } else {
          var targ = document.getElementById(target);
          if (targ && payload.firstChild) {
            if (target == "Ajax") {
              targ.innerHTML += payload.firstChild.nodeValue;
            } else {
              range.selectNodeContents(targ);
              range.deleteContents();
              targ.innerHTML = payload.firstChild.nodeValue;
            }
          }
        }
      }
    }
  }
  xmlHttp.open("GET",Responder+"?"
    +"&Message="+urlencode(Message),true);
  var ajax = document.getElementById("Ajax");
  if (ajax)
    ajax.innerHTML
      = "<table>"
      +"<tr><td>Message:</td><td>"+xmlencode(Message)+"</td></tr>"
      +"</table>";
  xmlHttp.send(null);
}
