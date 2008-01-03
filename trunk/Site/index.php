<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN" "http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <link rel="stylesheet" href="Apathy.css">
    <script type="text/javascript" src="ajax.js"></script>
  </head>
  <body
    onLoad="ajaxFunction('loader.php',
              '<reply><response><code>Initialize</code></response></reply>')">
    <div class="Main">
      <math xmlns="http://www.w3.org/1998/Math/MathML">
        <mrow>
          <msup>
            <mi>LVL</mi>
            <mn>2</mn>
          </msup>
          <mo>&#215;</mo>
          <mn>1000</mn>
        </mrow>
      </math>
      <div id="Path" class="Path"></div>
      <table class='MainPart'>
        <tbody>
          <tr>
            <td rowspan='2' valign='top'>
              <div id="Selector" class="Selector"></div>
            </td>
            <td valign='top'>
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
          <tr>
            <td valign='top'><div id="Display" class="Display"></div></td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>