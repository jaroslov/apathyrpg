function markInteresting(UpId,TaId) {
  var what = document.getElementById(UpId);
  what.style.fontStyle = "italic";
  what.style.borderTopColor = "red";
  what.style.borderBottomColor = "red";
  var what = document.getElementById(TaId);
  what.style.borderTopColor = "red";
  what.style.borderBottomColor = "red";
}
function markUninteresting(UpId,TaId) {
  var what = document.getElementById(UpId);
  what.style.fontStyle = "normal";
  what.style.borderColor = "black";
  what.style.borderTopColor = "blue";
  what.style.borderBottomColor = "blue";
  var what = document.getElementById(TaId);
  what.style.borderColor = "black";
  what.style.borderTopColor = "gray";
  what.style.borderBottomColor = "gray";
}
function initialLoad() {
  ajaxFunction('loader.php',
    '<reply><response><code>Initialize</code></response></reply>');
}
function realUnescape(string) {
  var str = string.replace(/\\/,"");
  //var str = string.replace(/\</,"&lt;");
  //var str = string.replace(/\>/,"&gt;");
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
          } else if (code == "Title")
            document.title = payload.firstChild.nodeValue;
        } else {
          var targ = document.getElementById(target);
          if (targ) {
            range.selectNodeContents(targ);
            range.deleteContents();
            targ.innerHTML = payload.firstChild.nodeValue;
          }
          if (logresponse)
            logresponse.innerHTML = realUnescape(xmlR);
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
      +"<tr><td>Message:</td><td>"+Message+"</td></tr>"
      +"</table>";
  xmlHttp.send(null);
}
function insertXmlIntoTarget(targ,payXml) {
  var node = document.createElement("p");
  var text = document.createTextNode("FOO");
  node.appendChild(text);
  targ.appendChild(node);
}
function focusStyle(element) {
  element.style.backgroundColor = "#FFFFFF";
  element.style.borderWidth = "1px";
  element.style.borderColor = "blue";
  element.style.backgroundImage = 'url(highlight-textarea.png)';
  element.style.backgroundRepeat = 'repeat-x';
  element.style.backgroundPosition = 'top';
}
function blurStyle(element) {
  element.style.backgroundColor = "#FFFFFF";
  element.style.borderColor = "gray";
  element.style.borderWidth = "1px";
  element.style.backgroundPosition = 'bottom';
  element.style.backgroundImage = 'url(pin-2x2.png)';
}