<html>
  <head>
    <link rel="stylesheet" href="Apathy.css">
    <script type="text/javascript" src="ajax.js"></script>
  </head>
  <body
    onLoad="ajaxFunction('loader.php',
              '<reply><response><code>Initialize</code></response></reply>')">
    <div class="Main">
      <div id="Path" class="Path"></div>
      <table class='MainPart'>
        <tbody>
          <tr>
            <td><div id="Selector" class="Selector"></div></td>
            <td><div id="Datum" class="Datum"></div></td>
          </tr>
        </tbody>
      </table>
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
    </div>
  </body>
</html>