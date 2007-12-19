<html>
  <head>
    <style>
      .DisplayForm {
        border: 1px solid black;
        border-width: 0px;
        border-spacing: 0px;
        border-collapse: collapse;
      }
    </style>
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
  <body onLoad="ajaxFunction('body','Body','LoadCategory:Content')">
    <div id="Body">
    </div>
  </body>
</html>