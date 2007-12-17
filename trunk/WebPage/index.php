<html>
  <head>
    <script type="text/javascript">
    function ajaxFunction() {
      var xmlHttp;
      try {
        xmlHttp=new XMLHttpRequest();
      } catch (e) {
        try {
          xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
          try {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
          } catch (e) {
            alert("Your browser does not support AJAX!");
            return false;
          }
        }
      }
      xmlHttp.onreadystatechange = function () {
        if (4 == xmlHttp.readyState) {
          document.myForm.time.value = xmlHttp.responseText ;
        }
      }
      xmlHttp.open("GET","time.py",true);
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
      Name: <input type="text" onkeyup="ajaxFunction();" name="username" />
      Time: <input type="text" name="time" />
      </form>
    </div>
  </body>
</html>