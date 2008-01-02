function realUnescape(string) {
  var str = string.replace(/\\/,"");
  return str;
}
function urlencode(str) {
  str = escape(str);
  // javascript is megagay
  for (i = 0; i<str.length; i++) {
    str = str.replace('%20', '+');
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
      //var log = document.getElementById('LogResponse');
      //if (log)
      //  ;//log.innerHTML = realUnescape(xmlR);
      for (i = 0; i<replies.length; i++) {
        replies[i].normalize();
        var target = replies[i].getElementsByTagName("target")[0].firstChild.nodeValue;
        var payload = replies[i].getElementsByTagName("payload")[0].firstChild.nodeValue;
        if ("@" == target[0]) {
          codes = target.split("@");
          code = codes[1];
          if (code == "Focus") {
            document.getElementById(codes[2]).focus();
          } else if (code == "Title")
            document.title = payload;
        } else {
          var targ = document.getElementById(target);
          if (targ)
            targ.innerHTML = realUnescape(payload);
          //var log = document.getElementById('LogResponse');
          //if (log)
            //log.innerHTML = realUnescape(xmlR);
        }
      }
    }
  }
  xmlHttp.open("GET",Responder+"?"
    +"&Message="+urlencode(Message),true);
  /*document.getElementById("Ajax").innerHTML
    = "<table>"
    +"<tr><td>Message:</td><td>"+urlencode(Message)+"</td></tr>"
    +"</table>";*/
  xmlHttp.send(null);
}
function focusStyle(element) {
  element.style.backgroundColor = "#FFFFFF";
  element.style.borderWidth = "1px";
  element.style.borderColor = "blue";
  element.style.backgroundImage = 'url(highlight-textarea.png)';
  element.style.backgroundRepeat = 'repeat-x';
  //element.style.backgroundAttachment = 'fixed';
  element.style.backgroundPosition = 'top';
}
function blurStyle(element) {
  element.style.backgroundColor = "#FFFFFF";
  element.style.borderColor = "gray";
  element.style.borderWidth = "1px";
  element.style.backgroundPosition = 'bottom';
  element.style.backgroundImage = 'url(pin-2x2.png)';
}