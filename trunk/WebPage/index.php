<html>
  <head>
    <script type="text/javascript">
    function realUnescape(string) {
      var parts = unescape(string).split("\\");
      var nstr = ""
      for (var idx=0; idx<parts.length; idx=idx+1) {
        nstr = nstr + parts[idx];
      }
      return nstr;
    }
    function ajaxFunction(Source,Target,Message) {
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
        +"&message="+Message,true);
      xmlHttp.send(null);
    }
    </script>
  </head>
  <body onLoad="ajaxFunction('body','CategoryChoices','LoadCategory/Choices')">
    <div>
      <a href="apathy.xhtml">Apathy</a> will go here.<br/>
    </div>
    <div>
      <select id="CategoryChoices"
        onChange="ajaxFunction(id,'Body',value)">
      </select>
    </div>
    <div id="Body">
    </div>
  </body>
</html>