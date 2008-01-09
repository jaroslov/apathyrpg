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
function toggleOpacity(Id) {
  var who = document.getElementById(Id);
  if (who.style.opacity < .95)
    who.style.opacity = .95;
  else
    who.style.opacity = .65;
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
