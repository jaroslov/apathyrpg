<?xml version="1.0" encoding="ISO-8859-1" ?>
<!DOCTYPE stylesheet [
<!ENTITY rarr  "&#8594;" ><!-- small n, tilde -->
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/">
    <html>
      <head>
        <title>Apathy Manual (ARPG)</title>
        <link rel="stylesheet" type="text/css" href="Apathy.css" title="Apathy" />
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
        <!--<h1><xsl:value-of select="title" /></h1>-->
        <xsl:for-each select="chapter">
          <div class="chapter" id="chapter-{./title}">
            <!--<h1><xsl:value-of select="title" /></h1>-->
            <xsl:apply-templates />
          </div>
        </xsl:for-each>
      </div>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="section">
    <div class="section" id="section-{../title}-{./title}" >
      <!--<h1><xsl:value-of select="title" /></h1>-->
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
      <xsl:apply-templates select="text" />
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

  <xsl:template match="itemized-list">
    <ul class="itemized-list">
      <xsl:for-each select="item">
        <li>
          <xsl:apply-templates select="." />
        </li>
      </xsl:for-each>
    </ul>
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
    <table class="content-table">
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
            <xsl:variable name="cellspan" select="./@span" />
            <xsl:variable name="border" select="./@border" />
            <td colspan="{$cellspan}" class="{$border}" >
              <xsl:apply-templates />
            </td>
          </xsl:for-each>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>
  
  <xsl:template match="sup">
    <sup><xsl:value-of select="." /></sup>
  </xsl:template>
  
  <xsl:template match="title">
    <h1 class="bare-title"><xsl:value-of select="." /></h1>
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
  
  <xsl:template match="reference">
    <!-- A unique hrid to the category we need -->
    <xsl:variable name="hrid" select="./@hrid" />
    <table class="datum-table">
      <thead>
        <!-- Build the head from the Default structure -->
        <xsl:for-each select="//category[@name=$hrid]/default/field">
          <xsl:if test="./@title">
            <th><xsl:value-of select="@name" /></th>
          </xsl:if>
          <xsl:if test="./@table">
            <th class="content"><xsl:value-of select="@name" /></th>
          </xsl:if>
        </xsl:for-each>
      </thead>
      <!-- Build the body of the table from the datums found -->
      <xsl:for-each select="//category[@name=$hrid]/datum">
        <tr>
          <xsl:for-each select="field" >
            <xsl:if test="./@title">
              <td>
                <a href="#{../../../@name}-{$hrid}-{.}">
                  <xsl:apply-templates select="." />
                </a>
              </td>
            </xsl:if>
            <xsl:if test="./@table">
              <td class="content"><xsl:apply-templates select="." /></td>
            </xsl:if>
          </xsl:for-each>
        </tr>
      </xsl:for-each>
    </table>
    <!-- Builds the descriptor lists -->
    <xsl:for-each select="//category[@name=$hrid]/datum">
      <xsl:variable name="datum-title" select="field[@title='yes']" />
      <div class="datum-description"
        id="{../../@name}-{$hrid}-{$datum-title}">
        <h1><xsl:value-of select="field[@title='yes']" /></h1>
        <p><xsl:value-of select="field[@description='yes']" /></p>
      </div>
    </xsl:for-each>
  </xsl:template>

  <!-- BROKEN! Fix with MathML -->
  <xsl:template match="math">
    <span class="math">
      <xsl:apply-templates />
    </span>
  </xsl:template>
  
  <xsl:template match="sum">
    <table class="sum">
      <tr>
        <td colspan="2"><xsl:apply-templates select="./to" /></td>
      </tr>
      <tr>
        <td><span style="font-size:26pt; font-style: normal;"><xsl:apply-templates select="./symbol" /></span></td>
          <td><xsl:apply-templates select="./of" /></td>
      </tr>
      <tr>
        <td colspan="2"><xsl:apply-templates select="./from" /></td><td></td>
      </tr>
    </table>
  </xsl:template>
  
  <xsl:template match="parenthesis">
    (<xsl:value-of select="." />)
  </xsl:template>
  
  <xsl:template match="frac">
    <table class="fraction">
      <tr>
        <td class="numerator">
          <xsl:apply-templates select="top" />
        </td>
      </tr>
      <tr>
        <td class="denominator">
          <xsl:apply-templates select="bot" />
        </td>
      </tr>
    </table>
  </xsl:template>
  <!-- BROKEN! Fix with MathML -->

</xsl:stylesheet>