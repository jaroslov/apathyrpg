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
              document.title = "BEFORE...";
              who.innerHTML += payload.firstChild.nodeValue
              document.title += "AFTER";
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
