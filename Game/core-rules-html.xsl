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
          padding-right: 3em;
          padding-left: 3em;
          margin: 0em;
          font-size: 12pt;
          text-align: justify;
          font-color: black;
          background-color: white;
        }
        a {
          color: black;
          text-decoration: none;
        }
        a:hover {
          text-decoration: underline;
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
        table.apathy-authors {
          position: relative;
          text-align: center;
          right: -11em;
          top: -3em;
        }
        div.titlepage {
          border: double black;
          padding: 2em;
          padding-bottom: 5em;
        }
        div.part h1 {
          font-size: 28pt;
        }
        div.chapter h1 {
          font-size: 22pt;
        }
        div.section h1 {
          font-size: 16pt;
        }
        div.example {
          width: 35em;
          text-align: justify;
          padding: 2em;
        }
        div.example h1 {
          font-size: 14pt;
        }
        div.example p {
          padding-left: 2em;
          text-indent: -1em;
        }
        div.example span.example-title {
          font-variant: small-caps;
          font-weight: bold;
          padding-right: 1em;
        }
        span.ApAthy {
          font-variant: small-caps;
          font-weight: bold;
        }
        span.definition {
          font-style: italic;
        }
        .description-term {
          font-weight: bold;
        }
        p.figure-caption {
          font-weight: bold;
          text-align: center;
          font-variant: small-caps;
        }
        div.note {
          padding: 1em;
          padding-left: 3em;
          padding-right: 3em;
          border: 1px solid red;
          margin: .5em;
        }
        div.note p {
          margin: 0;
          padding: 0;
        }
        div.note span.note-exclaim {
          font-weight: bold;
          font-variant: small-caps;
          font-size: 16pt;
          margin-right: .5em;
        }
        </style>
      </head>
      <body>
        <div class="titlepage">
          <span class="apathy-A1">A</span>
          <span class="apathy-P">P</span>
          <span class="apathy-A2">A</span>
          <span class="apathy-thy">THY</span>
          <table class="apathy-authors">
            <tr><td>Allan Moyse</td></tr>
            <tr><td>Jacob Smith</td></tr>
            <tr><td>Nathan Jones</td></tr>
            <tr><td>Noah Smith</td></tr>
            <tr><td>Chris Cook</td></tr>
            <tr><td>Josh Kramer</td></tr>
          </table>
        </div>
        <div class="toc">
          <h1>Table of Contents</h1>
          <ol class="toc" >
            <xsl:for-each select="//chapter">
              <li>
                <a href="#chapter-{./title}">
                  <xsl:value-of select="title" />
                </a>
                <ol class="toc" >
                  <xsl:for-each select="section">
                    <li>
                      <a href="#section-{../title}-{./title}">
                        <xsl:value-of select="title" />
                      </a>
                      <ol class="toc" >
                        <xsl:for-each select="section">
                          <li>
                            <a href="#section-{../title}-{./title}">
                              <xsl:value-of select="title" />
                            </a>
                          </li>
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
              <li><a href="#example-{./title}">
                <xsl:value-of select="title" />
              </a></li>
            </xsl:for-each>
          </ol>
        </div>
        <div class="figures">
          <h1>List of Figures</h1>
          <ol class="figures">
            <xsl:for-each select="//figure">
              <li>
                <a href="#figure-{./caption}">
                  <xsl:value-of select="caption" />
                </a>
              </li>
            </xsl:for-each>
          </ol>
        </div>
        <xsl:apply-templates />
      </body>
    </html>
  </xsl:template>
  
  <xsl:template match="arpg">
    <xsl:for-each select="part">
      <div class="part">
        <h1><xsl:value-of select="title" /></h1>
        <xsl:for-each select="chapter">
          <div class="chapter" id="chapter-{./title}">
            <h1><xsl:value-of select="title" /></h1>
            <xsl:apply-templates />
          </div>
        </xsl:for-each>
      </div>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="section">
    <div class="section" id="section-{../title}-{./title}" >
      <h1><xsl:value-of select="title" /></h1>
      <xsl:apply-templates />
    </div>
  </xsl:template>
  
  <xsl:template match="text">
    <p>
      <xsl:apply-templates />
    </p>
  </xsl:template>

  <xsl:template match="example">
    <div class="example" id="example-{./title}">
      <h1>
        <span class="example-title">Example:</span>
        <xsl:value-of select="title" />
      </h1>
      <xsl:apply-templates />
    </div>
  </xsl:template>

  <xsl:template match="title">
  </xsl:template>

  <xsl:template match="Apathy">
    <span class="ApAthy">ApAthy</span>
  </xsl:template>

  <xsl:template match="description-list">
    <table class="description-list">
    <xsl:for-each select="item">
      <tr>
        <td class="description-term">
          <xsl:apply-templates select="description" />
        </td>
        <td class="description-definition">
          <xsl:apply-templates select="text" />
        </td>
      </tr>
    </xsl:for-each>
    </table>
  </xsl:template>

  <xsl:template match="define">
    <span class="definition" id=".">
      <xsl:value-of select="." />
    </span>
  </xsl:template>

  <xsl:template match="figure">
    <div class="figure" id="figure-{./caption}">
      <xsl:apply-templates select="table" />
      <xsl:apply-templates select="caption" />
    </div>
  </xsl:template>
  
  <xsl:template match="table">
    <table>
      <thead>
        <xsl:for-each select="head/cell">
          <th>
            <xsl:apply-templates />
          </th>
        </xsl:for-each>
      </thead>
      <xsl:for-each select="row">
        <tr>
          <xsl:for-each select="cell">
            <td>
              <xsl:apply-templates />
            </td>
          </xsl:for-each>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>
  
  <xsl:template match="caption">
    <p class="figure-caption"><xsl:value-of select="." /></p>
  </xsl:template>

  <xsl:template match="note">
    <div class="note">
      <p>
        <span class="note-exclaim">Note!</span>
        <xsl:apply-templates />
      </p>
    </div>
  </xsl:template>

</xsl:stylesheet>
