function realUnescape(string) {
  var parts = unescape(string).split("\\");
  var nstr = ""
  for (var idx=0; idx<parts.length; idx=idx+1) {
    nstr = nstr + parts[idx];
  }
  return nstr;
}
function ajaxFunction(Responder,Source,Target,Code,Message) {
  xmlHttp=new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (4 == xmlHttp.readyState) {
      var xmlR = xmlHttp.responseText;
      var domp = new DOMParser();
      var responseXml = domp.parseFromString(xmlR, "text/xml");
      var replies = responseXml.getElementsByTagName("response");
      var log = document.getElementById('LogResponse');
      if (log)
        log.innerHTML = realUnescape(xmlR);
      for (i = 0; i<replies.length; i++) {
        var target = replies[i].getElementsByTagName("target")[0].firstChild.nodeValue;
        var payload = replies[i].getElementsByTagName("payload")[0].firstChild.nodeValue;
        if ("@" == target[0]) {
          target = target.split("@")[1];
          document.title = payload;
        } else {
          var targetNode = document.getElementById(target);
          targetNode.innerHTML = realUnescape(payload);
          //targetNode.firstChild = createElementFromString(payload);
          var log = document.getElementById('LogResponse');
          if (log)
            log.innerHTML = realUnescape(xmlR);
        }
      }
    }
  }
  xmlHttp.open("GET",Responder+"?"
    +"responder="+Responder
    +"&source="+Source
    +"&target="+Target
    +"&code="+Code
    +"&message="+Message,true);
  document.getElementById("Ajax").innerHTML
    = "<table>"
    +"<tr><td>Responder:</td><td>"+Responder+"</td></tr>"
    +"<tr><td>Source:</td><td>"+Source+"</td></tr>"
    +"<tr><td>Target:</td><td>"+Target+"</td></tr>"
    +"<tr><td>Code:</td><td>"+Code+"</td></tr>"
    +"<tr><td>Message:</td><td>"+Message+"</td></tr>"
    +"</table>";
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