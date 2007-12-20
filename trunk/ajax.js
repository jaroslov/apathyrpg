function realUnescape(string) {
  var parts = unescape(string).split("\\");
  var nstr = ""
  for (var idx=0; idx<parts.length; idx=idx+1) {
    nstr = nstr + parts[idx];
  }
  return nstr;
}
function ajaxFunction(Source,Target,Code,Message) {
  document.title = "{"+Source+"#"+Target+"#"+Code+"#"+Message+"}";
  xmlHttp=new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (4 == xmlHttp.readyState) {
      var xmlR = xmlHttp.responseText;
      var domp = new DOMParser();
      var responseXml = domp.parseFromString(xmlR, "text/xml");
      var target = responseXml.getElementsByTagName("target")[0].firstChild.nodeValue;
      var payload = responseXml.getElementsByTagName("payload")[0].firstChild.nodeValue;
      document.getElementById(target).innerHTML = realUnescape(payload);
    }
  }
  xmlHttp.open("GET","ajax.php?source="+Source
    +"&target="+Target
    +"&code="+Code
    +"&message="+Message,true);
  xmlHttp.send(null);
}