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
            <td rowspan='2'><div id="Selector" class="Selector"></div></td>
            <td><div id="Display" class="Display"></div></td>
          </tr>
          <tr>
            <td>
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
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>