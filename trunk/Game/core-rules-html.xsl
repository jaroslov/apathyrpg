<?xml version="1.0" encoding="ISO-8859-1" ?>
<!DOCTYPE stylesheet [
<!ENTITY rarr  "&#8594;" ><!-- small n, tilde -->
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/">
    <html>
      <head>
        <style>
        body {
          padding: 1em;
          padding-left: 3em;
          margin: 0em;
          font-size: 12pt;
        }
        ol {
          font-size: 12pt;
        }
        div.toc h1 {
          font-variant: small-caps;
          font-size: 16pt;
        }
        ol.toc {
          list-style-position: outside;
          list-style-type: upper-roman;
          margin-left: 1em;
          padding-left: .1em;
        }
        ol.toc ol {
          list-style-type: lower-roman;
        }
        ol.toc ol ol {
          list-style-type: lower-alpha;
        }
        ol.toc li {
          font-variant: small-caps;
        }
        div.examples h1 {
          font-variant: small-caps;
          font-size: 16pt;
        }
        ol.examples {
          list-style-position: outside;
          list-style-type: upper-roman;
          margin-left: 1em;
          padding-left: .1em;
        }
        ol.examples li {
          font-variant: small-caps;
        }
        div.figures h1 {
          font-variant: small-caps;
          font-size: 16pt;
        }
        ol.figures {
          list-style-position: outside;
          list-style-type: upper-roman;
          margin-left: 1em;
          padding-left: .1em;
        }
        ol.figures li {
          font-variant: small-caps;
        }
        span.apathy-A1 {
          font-size: 90pt;
        }
        span.apathy-P {
          position: relative;
          font-size: 60pt;
          left: -.22em;
          top: -.25em;
        }
        span.apathy-A2 {
          position: relative;
          font-size: 90pt;
          left: -.4em;
        }
        span.apathy-thy {
          position: relative;
          font-size: 60pt;
          left: -1em;
          top: -.25em;
        }
        span.apathy-Allan {
          position: relative;
          left: -12em;
        }
        span.apathy-Jacob {
          position: relative;
          font-size: 12pt;
          left: -17em;
          top: 1em;
        }
        span.apathy-Nathan {
          position: relative;
          font-size: 12pt;
          left: -22em;
          top: 2em;
        }
        span.apathy-Noah {
          position: relative;
          font-size: 12pt;
          left: -27em;
          top: 3em;
        }
        span.apathy-Chris {
          position: relative;
          font-size: 12pt;
          left: -31.7em;
          top: 4em;
        }
        span.apathy-Josh {
          position: relative;
          font-size: 12pt;
          left: -36.5em;
          top: 5em;
        }
        div.titlepage {
          border: double black;
          padding: 2em;
          padding-bottom: 5em;
        }
        </style>
      </head>
      <body>
        <div class="titlepage">
          <span class="apathy-A1">A</span>
          <span class="apathy-P">P</span>
          <span class="apathy-A2">A</span>
          <span class="apathy-thy">THY</span>
          <span class="apathy-Allan">Allan Moyse</span>
          <span class="apathy-Jacob">Jacob Smith</span>
          <span class="apathy-Nathan">Nathan Jones</span>
          <span class="apathy-Noah">Noah Smith</span>
          <span class="apathy-Chris">Chris Cook</span>
          <span class="apathy-Josh">Josh Kramer</span>
        </div>
        <div class="toc">
          <h1>Table of Contents</h1>
          <ol class="toc" >
            <xsl:for-each select="//chapter">
              <li>
                <xsl:value-of select="title" />
                <ol class="toc" >
                  <xsl:for-each select="section">
                    <li>
                      <xsl:value-of select="title" />
                      <ol class="toc" >
                        <xsl:for-each select="section">
                          <li><xsl:value-of select="title" /></li>
                        </xsl:for-each>
                      </ol>
                    </li>
                  </xsl:for-each>
                </ol>
              </li>
            </xsl:for-each>
          </ol>
        </div>
        <div class="examples">
          <h1>List of Examples</h1>
          <ol class="examples">
            <xsl:for-each select="//example">
              <li><xsl:value-of select="title" /></li>
            </xsl:for-each>
          </ol>
        </div>
        <div class="figures">
          <h1>List of Figures</h1>
          <ol class="figures">
            <xsl:for-each select="//figure">
              <li><xsl:value-of select="caption" /></li>
            </xsl:for-each>
          </ol>
        </div>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
