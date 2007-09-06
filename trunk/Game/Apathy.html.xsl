<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">

  <xsl:output version="1.0"
    method="xml"
    encoding="ISO-8859-1"
    media-type="text/html"
    indent="yes"
    doctype-public="-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN"
    doctype-system="http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd"/>
  <xsl:preserve-space elements="html head body" />
  <xsl:strip-space elements="p a" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
      <head>
        <title>Apathy Manual (ARPG)</title>
        <link rel="stylesheet" type="text/css" href="Apathy.css" title="Apathy" />
      </head>
      <body>
        <xsl:apply-templates select="//book" />      
      </body>
    </html>
  </xsl:template>

  <xsl:template match="book">
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
      <li><a href="#List-of-Equations">List of Equations</a></li>
    </ul>
    <div class="toc" id="Table-of-Contents">
      <h1>Table of Contents</h1>
      <ol class="toc">
        <xsl:for-each select="//part">
          <li>
            <a href="#{generate-id(.)}">
              <xsl:value-of select="title" />
            </a>
            <ol class="toc" >
              <xsl:for-each select="chapter">
                <li>
                  <a href="#{generate-id(.)}">
                    <xsl:value-of select="title" />
                  </a>
                  <ol class="toc" >
                    <xsl:for-each select="section">
                      <li>
                        <a href="#{generate-id(.)}">
                          <xsl:value-of select="title" />
                        </a>
                        <ol class="toc" >
                          <xsl:for-each select="section">
                            <li>
                              <a href="#{generate-id(.)}">
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
          </li>
        </xsl:for-each>
      </ol>
    </div>
    <div class="examples" id="List-of-Examples">
      <h1>List of Examples</h1>
      <ol class="examples">
        <xsl:for-each select="//example">
          <li><a href="#{generate-id(.)}">
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
            <a href="#{generate-id(.)}">
              <xsl:value-of select="caption" />
            </a>
          </li>
        </xsl:for-each>
      </ol>
    </div>
    <div class="equations" id="List-of-Equations">
      <h1>List of Equations</h1>
      <ol class="equations">
        <xsl:for-each select="//math">
          <li>
            <a href="#{generate-id(.)}">
              <xsl:apply-templates />
            </a>
          </li>
        </xsl:for-each>
      </ol>
    </div>
    <xsl:apply-templates select="part" />
    <div class="footnotes">
      <xsl:for-each select="//footnote">
        <div class="footnote" id="{generate-id(.)}">
          <p><xsl:number value="position()" /><xsl:apply-templates /></p>
        </div>
      </xsl:for-each>
    </div>
  </xsl:template>

  <!-- Inline elements -->
  <!-- special "Apathy" word -->
  <xsl:template match="Apathy">
    <span class="ApAthy">Apathy</span>
  </xsl:template>
  <!-- dice rolls -->
  <xsl:template match="roll">
    <span class="roll">
      <span class="num">
        <xsl:value-of select="num" />
      </span>
      <span class="D">D</span>
      <span class="face">
        <xsl:value-of select="face" />
      </span>
      <span class="bns">
        <xsl:value-of select="bns" />
      </span>
      <span class="kind">
        <xsl:value-of select="kind" />
      </span>
    </span>
  </xsl:template>
  <!-- "n/a" notappl -->
  <xsl:template match="notappl">
    <span class="notappl">n/a</span>
  </xsl:template>
  <!-- define -->
  <xsl:template match="define">
    <span class="definition" id=".">
      <xsl:value-of select="." />
    </span>
  </xsl:template>
  <!-- all titles -->
  <xsl:template match="title">
    <h1><xsl:apply-templates /></h1>
  </xsl:template>
  <!-- footnote -->
  <xsl:template match="footnote">
    <sup><a class="footnote" href="#{generate-id(.)}">
      <xsl:variable name="index">
        <xsl:number/>
      </xsl:variable>
      <xsl:copy-of select="$index"/>
    </a></sup>
  </xsl:template>

  <!-- structure elements -->
  <!-- part -->
  <xsl:template match="part">
    <div class="part" id="{generate-id(.)}" >
      <xsl:apply-templates />
    </div>
  </xsl:template>
  <!-- chapter -->
  <xsl:template match="chapter">
    <div class="chapter" id="{generate-id(.)}" >
      <xsl:apply-templates />
    </div>
  </xsl:template>
  <!-- section -->
  <xsl:template match="section">
    <div class="section" id="{generate-id(.)}" >
      <xsl:apply-templates />
    </div>
  </xsl:template>

  <!-- Block Non-Structure -->
  <!-- text blocks -->
  <xsl:template match="text">
    <p><xsl:apply-templates /></p>
  </xsl:template>
  <!-- examples -->
  <xsl:template match="example">
    <div class="example" id="{generate-id(.)}">
      <h1>
        <span class="example-title">Example:</span>
        <xsl:value-of select="title" />
      </h1>
      <xsl:apply-templates select="text" />
    </div>
  </xsl:template>
  <!-- Note -->
  <xsl:template match="note">
    <div class="note">
      <p>
        <span class="note-exclaim">Note!</span>
        <xsl:apply-templates />
      </p>
    </div>
  </xsl:template>
  
  <!-- LIST KINDS -->
  <!-- description-lists -->
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
  <!-- itemized lists -->
  <xsl:template match="itemized-list">
    <ul class="itemized-list">
      <xsl:for-each select="item">
        <li>
          <xsl:apply-templates select="." />
        </li>
      </xsl:for-each>
    </ul>
  </xsl:template>
  <!-- numbered lists -->
  <xsl:template match="numbered-list" >
    <ol class="numbered-list">
      <xsl:for-each select="item">
        <li>
          <xsl:apply-templates select="." />
        </li>
      </xsl:for-each>
    </ol>    
  </xsl:template>

  <!-- Figure -->
  <!-- figure -->
  <xsl:template match="figure">
    <div class="figure" id="{generate-id(.)}">
      <xsl:apply-templates select="table" />
      <xsl:apply-templates select="caption" />
    </div>
  </xsl:template>
  <!-- caption -->
  <xsl:template match="caption">
    <p class="figure-caption"><xsl:value-of select="." /></p>
  </xsl:template>
  <!-- tables -->
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

	<!--
		Given a reference to the raw-data section, we build
		a table, then build a descriptor-list.
	-->
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
                <a href="#{../../../@name}-{$hrid}-{translate(.,' ','')}">
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
        id="{../../@name}-{$hrid}-{translate($datum-title,' ','')}">
        <span class="datum-description"><xsl:value-of select="field[@title='yes']" /></span>
        <p><xsl:value-of select="field[@description='yes']" /></p>
      </div>
    </xsl:for-each>
  </xsl:template>

  <!-- math -->
  <xsl:template match="math">
    <span id="{generate-id(.)}">
      <xsl:element name="math"
        namespace="http://www.w3.org/1998/Math/MathML">
        <xsl:apply-templates />
      </xsl:element>
    </span>
  </xsl:template>
  <xsl:template match="mrow">
    <xsl:element name="mrow"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="mi">
    <xsl:element name="mi"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="mo">
    <xsl:element name="mo"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="mn">
    <xsl:element name="mn"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="msup">
    <xsl:element name="msup"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="munderover">
    <xsl:element name="munderover"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="mfrac">
    <xsl:element name="mfrac"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="mstyle">
    <xsl:element name="mstyle"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:attribute name="scriptlevel"><xsl:value-of select="@scriptlevel" /></xsl:attribute>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>