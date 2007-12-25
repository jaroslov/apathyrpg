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
  <xsl:strip-space elements="p a span" />

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
      <img src="Apathy.png" width="100%" />
      <table class="apathy-authors">
        <tr><td align='center'><pre>Allan Moyse</pre></td></tr>
        <tr><td align='center'><pre>Nathan Jones</pre></td></tr>
        <tr><td align='center'><pre>Jacob Smith</pre></td></tr>
        <tr><td align='center'><pre>Noah Smith</pre></td></tr>
        <tr><td align='center'><pre>Chris Cook</pre></td></tr>
        <tr><td align='center'><pre>Josh Kramer</pre></td></tr>
      </table>
    </div>
    <ul class="Fast-Find-List">
      <li><a href="#Table-of-Contents">Table of Contents</a></li>
      <li><a href="#List-of-Examples">List of Examples</a></li>
      <li><a href="#List-of-Figures">List of Figures</a></li>
      <!--<li><a href="#List-of-Equations">List of Equations</a></li>
      <li><a href="#List-of-Rolls">List of Rolls</a></li>-->
    </ul>
    <div class="toc" id="Table-of-Contents">
      <h1>Table of Contents</h1>
      <ol class="toc">
        <xsl:for-each select="section">
          <xsl:variable name="pt-uid" select="title/@xml:id" />
          <li>
            <a href="#{generate-id(.)}" name="{$pt-uid}">
              <xsl:value-of select="title" />
            </a>
          </li>
        </xsl:for-each>
      </ol>
    </div>
    <div class="examples" id="List-of-Examples">
      <h1>List of Examples</h1>
      <ol class="examples">
        <xsl:for-each select="//example">
          <li><a href="#{generate-id(.)}">
            <xsl:apply-templates select="title/child::node()" />
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
              <xsl:apply-templates select="caption/child::node()" />
            </a>
          </li>
        </xsl:for-each>
      </ol>
    </div>
    <!--<div class="equations" id="List-of-Equations">
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
    <div class="rolls" id="List-of-Rolls">
      <h1>List of Rolls</h1>
      <ol class="rolls">
        <xsl:for-each select="//roll">
          <li>
            <a href="#{generate-id(.)}">
              <span class="roll"><xsl:apply-templates /></span>
            </a>
          </li>
        </xsl:for-each>
      </ol>
    </div>-->
    <xsl:apply-templates select="section" />
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
  <!-- special "C" word -->
  <xsl:template match="C"><span class="defined">C</span></xsl:template>
  <!-- special "plusminus" word -->
  <xsl:template match="plusminus">&#177;</xsl:template>
  <!-- special "and" word -->
  <xsl:template match="and">&amp;</xsl:template>
  <!-- special "dollar" word -->
  <xsl:template match="dollar">$</xsl:template>
  <!-- special "percent" word -->
  <xsl:template match="percent">%</xsl:template>
  <!-- special "rightarrow" word -->
  <xsl:template match="rightarrow">&#8594;</xsl:template>
  <!-- special "ldquo" word -->
  <xsl:template match="ldquo">&#8220;</xsl:template>
  <!-- special "rdquo" word -->
  <xsl:template match="rdquo">&#8221;</xsl:template>
  <!-- special "lsquo" word -->
  <xsl:template match="lsquo">&#8216;</xsl:template>
  <!-- special "rsquo" word -->
  <xsl:template match="rsquo">&#8217;</xsl:template>
  <!-- special "times" word -->
  <xsl:template match="times">&#215;</xsl:template>
  <!-- special "ouml" word -->
  <xsl:template match="ouml">&#246;</xsl:template>
  <!-- special "oslash" word -->
  <xsl:template match="oslash">&#248;</xsl:template>
  <!-- special "ndash" word -->
  <xsl:template match="ndash">&#8211;</xsl:template>
  <!-- special "mdash" word -->
  <xsl:template match="mdash">&#8212;</xsl:template>
  <!-- special "trademark" word -->
  <xsl:template match="trademark">&#8482;</xsl:template>
  <!-- special "mathematical sum" word -->
  <xsl:template match="Sum">&#8721;</xsl:template>
  <!-- dice rolls -->
  <xsl:template match="roll">
    <xsl:choose>
      <xsl:when test="./@type='alt'">
        <span class="roll" id="{generate-id(.)}">
          <xsl:apply-templates select="num"/>
          <xsl:apply-templates select="face"/>
          <xsl:apply-templates select="bOff"/>
          <xsl:apply-templates select="bns"/>
          <xsl:apply-templates select="mul"/>
          <xsl:apply-templates select="kind"/>
          <xsl:if test="raw">
            [<xsl:apply-templates select="rOff"/><xsl:apply-templates select="raw"/>]
          </xsl:if>
        </span>
      </xsl:when>
      <xsl:otherwise>
        <span class="roll" id="{generate-id(.)}">
          <xsl:apply-templates select="rOff"/>
          <xsl:apply-templates select="raw"/>
          <xsl:if test="raw">
            <span class="rawPlus">+</span>
          </xsl:if>
          <xsl:apply-templates select="num"/>
          <xsl:apply-templates select="face"/>
          <xsl:apply-templates select="bOff"/>
          <xsl:apply-templates select="bns"/>
          <xsl:apply-templates select="mul"/>
          <xsl:apply-templates select="kind"/>
        </span>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <xsl:template match="rOff">
    <span class="rOff">
      <xsl:apply-templates />
    </span>
  </xsl:template>
  <xsl:template match="raw">
    <span class="raw">
      <xsl:apply-templates />
    </span>
  </xsl:template>
  <xsl:template match="num">
    <span class="num">
      <xsl:apply-templates />
    </span>
  </xsl:template>
  <xsl:template match="face">
    <span class="D">D</span>
    <span class="face">
      <xsl:apply-templates />
    </span>
  </xsl:template>
  <xsl:template match="bOff">
    <span class="bOff">
      <xsl:apply-templates />
    </span>
  </xsl:template>
  <xsl:template match="bns">
    <span class="bns">
      <xsl:apply-templates />
    </span>
  </xsl:template>
  <xsl:template match="mul">
    <span class="mul">
      &#215;<xsl:apply-templates />
    </span>
  </xsl:template>
  <xsl:template match="kind">
    <span class="kind">
      <xsl:apply-templates />
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
    <xsl:variable name="gpkind" select="../../../@kind" />
    <xsl:variable name="pkind" select="../../@kind" />
    <xsl:variable name="kind" select="../@kind" />
    <h1 class="sector-{$gpkind}{$pkind}{$kind}"><xsl:apply-templates /></h1>
  </xsl:template>
  <!-- footnote -->
  <xsl:template match="footnote">
    <xsl:variable name="thisFN" select="." />
    <xsl:for-each select="//footnote">
      <xsl:if test="$thisFN=.">
        <sup><a class="footnote" href="#{generate-id(.)}">
          <xsl:number value="position()" />
        </a></sup>
      </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <!-- structure elements -->
  <!-- section -->
  <xsl:template match="section">
    <xsl:variable name="sec-uid" select="./@xml:id" />
    <xsl:variable name="kind" select="./@kind" />
    <div class="section" id="{generate-id(.)}" name="{$sec-uid}">
      <xsl:apply-templates select="title"/>
      <div class="toc" id="Table-of-Contents">
        <ol class="toc">
          <xsl:for-each select="section">
            <xsl:variable name="sub-uid" select="title/@xml:id" />
            <li>
              <a href="#{generate-id(.)}" name="{$sub-uid}">
                <xsl:value-of select="title" />
              </a>
            </li>
          </xsl:for-each>
        </ol>
      </div>
      <xsl:apply-templates select="section|reference|text|example|description-list|itemized-list|numbered-list|figure|equation|note"/>
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
        <xsl:apply-templates select="title" />
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
          <xsl:apply-templates select="text|numbered-list|description-list|note|example|table|figure|math" />
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
    <p class="figure-caption"><xsl:apply-templates /></p>
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
  <xsl:template match="summary">
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
                <a href="#{generate-id(.)}">
                  <xsl:apply-templates />
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
                <a href="#{generate-id(.)}">
                  <xsl:apply-templates />
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
      <xsl:variable name="title-id" select="generate-id(field)"/>
      <div class="datum-description"
        id="{$title-id}">
        <span class="datum-description"><xsl:value-of select="field[@title='yes']" /></span>
        <xsl:apply-templates select="field[@description='yes']" />
      </div>
    </xsl:for-each>
  </xsl:template>

  <!-- math -->
  <xsl:template match="equation">
    <div class="math-equation">
      <xsl:apply-templates />
    </div>
  </xsl:template>
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