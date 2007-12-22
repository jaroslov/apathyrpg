function realUnescape(string) {
  var parts = unescape(string).split("\\");
  var nstr = ""
  for (var idx=0; idx<parts.length; idx=idx+1) {
    nstr = nstr + parts[idx];
  }
  return nstr;
}
function ajaxFunction(Source,Target,Code,Message) {
  xmlHttp=new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (4 == xmlHttp.readyState) {
      var xmlR = xmlHttp.responseText;
      var domp = new DOMParser();
      var responseXml = domp.parseFromString(xmlR, "text/xml");
      var replies = responseXml.getElementsByTagName("response");
      for (i = 0; i<replies.length; i++) {
        var target = replies[i].getElementsByTagName("target")[0].firstChild.nodeValue;
        var payload = replies[i].getElementsByTagName("payload")[0].firstChild.nodeValue;
        if ("@" == target[0]) {
          target = target.split("@")[1];
          document.title = payload;
        } else {
          document.getElementById(target).innerHTML = realUnescape(payload);
          var log = document.getElementById('LogResponse');
          if (log)
            log.innerHTML = xmlR;
        }
      }
    }
  }
  xmlHttp.open("GET","ajax.php?source="+Source
    +"&target="+Target
    +"&code="+Code
    +"&message="+Message,true);
  document.getElementById("Ajax").innerHTML
    = "<table>"
    +"<tr><td>Source:</td><td>"+Source+"</td></tr>"
    +"<tr><td>Target:</td><td>"+Target+"</td></tr>"
    +"<tr><td>Code:</td><td>"+Code+"</td></tr>"
    +"<tr><td>Message:</td><td>"+Message+"</td></tr>"
    +"</table>";
  xmlHttp.send(null);
}
function focusStyle(element) {
  element.style.backgroundColor = "#FAFAFA";
  element.style.borderColor = "black";
}
function blurStyle(element) {
  element.style.backgroundColor = "#FFFFFF";
  element.style.borderColor = "gray";
}