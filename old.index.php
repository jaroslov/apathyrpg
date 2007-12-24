<html>
  <head>
    <link rel="stylesheet" href="Apathy.css">
    <script type="text/javascript" src="ajax.js"></script>
  </head>
  <body onLoad="ajaxFunction('ajax.php','MainPart','MainPart','Initialize','')">
    <div id="Path" class="Path"></div>
    <div id="Datum" class="Datum"></div>
    <table valign="top">
      <tr valign="top">
        <td>
          <div id="Ajax" class="Log" style="visibility:show;">
            <em>Ajax</em>
          </div>
        </td>
        <td>
          <div id="Log" class="Log" style="visibility:show;">
            <em>Log</em>
          </div>
        </td>
        <td>
          <div id="LogResponse" class="Log" style="visibility:show;">
            <em>Response</em>
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>