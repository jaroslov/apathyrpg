<html>
  <head>
    <script type="text/javascript">
    function findElementById(what) {
      return document.getElementById(what);
    }
    function ajaxFunction() {
      xmlHttp=new XMLHttpRequest();
      xmlHttp.onreadystatechange = function () {
        if (4 == xmlHttp.readyState) {
          var xmlR = xmlHttp.responseText;
          var domp = new DOMParser();
          var responseXml = domp.parseFromString(xmlR, "text/xml");
          var target = responseXml.getElementsByTagName("target")[0].firstChild.nodeValue;
          var payload = responseXml.getElementsByTagName("payload")[0].firstChild.nodeValue;
          document.getElementById(target).value = payload;
        }
      }
      xmlHttp.open("GET","time.php?username="+escape(document.myForm.username.value),true);
      xmlHttp.send(null);
    }
    </script>
  </head>
  <body>
    <div>
      <a href="apathy.xhtml">Apathy</a> will go here.<br/>
    </div>
    <div>
      <form name="myForm">
      Name: <input type="text" onkeyup="ajaxFunction();" id="username" />
      Time: <input type="text" id="time" />
      </form>
    </div>
  </body>
</html>