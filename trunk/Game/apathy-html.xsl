<?xml version="1.0" encoding="ISO-8859-1" ?>
<!DOCTYPE stylesheet [
<!ENTITY rarr  "&#8594;" ><!-- small n, tilde -->
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/">
    <html>
      <head>
        <title>Apathy Manual (ARPG)</title>
        <link rel="stylesheet" type="text/css" href="apathy.css" title="Apathy" />
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
        <ul class="Fast-Find-List">
          <li><a href="#Table-of-Contents">Table of Contents</a></li>
          <li><a href="#List-of-Examples">List of Examples</a></li>
          <li><a href="#List-of-Figures">List of Figures</a></li>
        </ul>
        <div class="toc" id="Table-of-Contents">
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
        <div class="examples" id="List-of-Examples">
          <h1>List of Examples</h1>
          <ol class="examples">
            <xsl:for-each select="//example">
              <li><a href="#example-{./title}">
                <xsl:value-of select="title" />
              </a></li>
            </xsl:for-each>
          </ol>
        </div>
        <div class="figures" id="List-of-Figures">
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
        <xsl:apply-templates select="//book"/>
      </body>
    </html>
  </xsl:template>
  
  <xsl:template match="book">
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
    <span class="ApAthy">Apathy</span>
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
