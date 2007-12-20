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
        <script lanaguage="javascript" src="ajax.js" />
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
        <tr><td>Nathan Jones</td></tr>
        <tr><td>Jacob Smith</td></tr>
        <tr><td>Noah Smith</td></tr>
        <tr><td>Chris Cook</td></tr>
        <tr><td>Josh Kramer</td></tr>
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
        <xsl:for-each select="//part">
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
          <xsl:variable name="ex-uid" select="title/@xml:id" />
          <li><a href="#{generate-id(.)}" name="{$ex-uid}">
            <xsl:apply-templates select="title/child::node()" />
          </a></li>
        </xsl:for-each>
      </ol>
    </div>
    <div class="figures" id="List-of-Figures">
      <h1>List of Figures</h1>
      <ol class="figures">
        <xsl:for-each select="//figure">
          <xsl:variable name="fig-uid" select="./@xml:id" />
          <li>
            <a href="#{generate-id(.)}" name="{$fig-uid}">
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
    <xsl:apply-templates select="part" />
    <div class="footnotes">
      <xsl:for-each select="//footnote">
        <xsl:variable name="foot-uid" select="title/@xml:id" />
        <div class="footnote" id="{generate-id(.)}" name="{$foot-uid}">
          <p><xsl:number value="position()" /><xsl:apply-templates /></p>
        </div>
      </xsl:for-each>
    </div>
  </xsl:template>

  <!-- Inline elements -->
  <!-- special "Apathy" word -->
  <xsl:template match="Apathy">{Apathy}</xsl:template>
  <!-- special "C" word -->
  <xsl:template match="C">{C}</xsl:template>
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
  <xsl:template match="times">*</xsl:template>
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
    {roll <xsl:if test="raw">
      [<xsl:apply-templates select="rOff"/><xsl:apply-templates select="raw"/>]
    </xsl:if>
    <xsl:apply-templates select="num"/>
    <xsl:apply-templates select="face"/>
    <xsl:apply-templates select="bOff"/>
    <xsl:apply-templates select="bns"/>
    <xsl:apply-templates select="mul"/>
    <xsl:apply-templates select="kind"/>}
  </xsl:template>
  <xsl:template match="rOff"><xsl:apply-templates /></xsl:template>
  <xsl:template match="raw"><xsl:apply-templates /></xsl:template>
  <xsl:template match="num"><xsl:apply-templates /></xsl:template>
  <xsl:template match="face">D<xsl:apply-templates /></xsl:template>
  <xsl:template match="bOff"><xsl:apply-templates /></xsl:template>
  <xsl:template match="bns"><xsl:apply-templates /></xsl:template>
  <xsl:template match="mul">&#215;<xsl:apply-templates /></xsl:template>
  <xsl:template match="kind"><xsl:apply-templates /></xsl:template>
  <!-- "n/a" notappl -->
  <xsl:template match="notappl">[n/a/]</xsl:template>
  <!-- define -->
  <xsl:template match="define">{def <xsl:value-of select="." />}</xsl:template>
  <!-- all titles -->
  <xsl:template match="title">
    <xsl:variable name="title-uid" select="./@xml:id" />
    <h1 name="{$title-uid}"><xsl:apply-templates /></h1>
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
  <!-- part -->
  <xsl:template match="part">
    <xsl:variable name="pt-uid" select="./@xml:id" />
    <div name="{$pt-uid}" id="{generate-id(.)}">
      <xsl:apply-templates select="title"/>
      <div class="toc" id="Table-of-Contents">
        <ol class="toc">
          <xsl:for-each select="chapter">
            <xsl:variable name="ch-uid" select="title/@xml:id" />
            <li>
              <a href="#{generate-id(.)}" name="{$ch-uid}">
                <xsl:value-of select="title" />
              </a>
            </li>
          </xsl:for-each>
        </ol>
      </div>
      <xsl:apply-templates select="chapter"/>
    </div>
  </xsl:template>
  <!-- chapter -->
  <xsl:template match="chapter">
    <xsl:variable name="ch-uid" select="./@xml:id" />
    <div class="chapter" id="{generate-id(.)}" name="{$ch-uid}">
      <xsl:apply-templates select="title" />
      <div class="toc" id="Table-of-Contents">
        <ol class="toc">
          <xsl:for-each select="section">
            <xsl:variable name="sec-uid" select="title/@xml:id" />
            <li>
              <a href="#{generate-id(.)}" name="{$sec-uid}">
                <xsl:value-of select="title" />
              </a>
            </li>
          </xsl:for-each>
        </ol>
      </div>
      <xsl:apply-templates select="section" />
    </div>
  </xsl:template>
  <!-- section -->
  <xsl:template match="section">
    <xsl:variable name="sec-uid" select="./@xml:id" />
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
      <xsl:apply-templates select="section|reference|text|example|description-list|itemized-list|numbered-list|figure|equation|note|table"/>
    </div>
  </xsl:template>

  <!-- Block Non-Structure -->
  <xsl:template match="text()">
    <xsl:value-of select="."/>
  </xsl:template>
  <!-- text blocks -->
  <xsl:template match="text">
    <xsl:variable name="text-uid" select="./@xml:id" />
    <p name="{$text-uid}" class="regular-text" id="{generate-id(.)}"
      onClick="ajaxFunction(id,id,'Click:text','{$text-uid}@'+id)">
        <xsl:apply-templates />
    </p>
  </xsl:template>
  <!-- examples -->
  <xsl:template match="example">
    <xsl:variable name="ex-uid" select="./@xml:id" />
    <div class="example" id="{generate-id(.)}" name="{$ex-uid}">
      <h1>
        <span class="example-title">Example:</span>
        <xsl:apply-templates select="title" />
      </h1>
      <xsl:apply-templates select="text" />
    </div>
  </xsl:template>
  <!-- Note -->
  <xsl:template match="note">
    <xsl:variable name="note-uid" select="./@xml:id" />
    <div class="note">
      <p name="{$note-uid}">
        <span class="note-exclaim">Note!</span>
        <xsl:apply-templates />
      </p>
    </div>
  </xsl:template>
  
  <!-- LIST KINDS -->
  <!-- description-lists -->
  <xsl:template match="description-list">
    <xsl:variable name="dlist-uid" select="./@xml:id" />
    <table class="description-list" name="{$dlist-uid}">
    <tr><td></td><td></td></tr>
    <xsl:for-each select="item">
      <xsl:variable name="ditem-uid" select="./@xml:id" />
      <xsl:variable name="desc-uid" select="description/@xml:id" />
      <tr name="{$ditem-uid}">
        <td class="description-term" name="{$desc-uid}">
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
    <xsl:variable name="ilist-uid" select="./@xml:id" />
    <ul class="itemized-list" name="{$ilist-uid}">
      <xsl:for-each select="item">
        <xsl:variable name="iitem-uid" select="./@xml:id" />
        <li name="{$iitem-uid}">
          <xsl:apply-templates select="." />
        </li>
      </xsl:for-each>
    </ul>
  </xsl:template>
  <!-- numbered lists -->
  <xsl:template match="numbered-list" >
    <xsl:variable name="num-uid" select="./@xml:id" />
    <ol class="numbered-list" name="{$num-uid}">
      <xsl:for-each select="item">
        <xsl:variable name="nitem-uid" select="./@xml:id" />
        <li name="{$nitem-uid}">
          <xsl:apply-templates select="." />
        </li>
      </xsl:for-each>
    </ol>    
  </xsl:template>

  <!-- Figure -->
  <!-- figure -->
  <xsl:template match="figure">
    <xsl:variable name="fig-uid" select="./@xml:id" />
    <div class="figure" id="{generate-id(.)}" name="{$fig-uid}">
      <xsl:apply-templates select="table" />
      <xsl:apply-templates select="caption" />
    </div>
  </xsl:template>
  <!-- caption -->
  <xsl:template match="caption">
    <xsl:variable name="cap-uid" select="./@xml:id" />
    <p class="figure-caption" name="{$cap-uid}"><xsl:apply-templates /></p>
  </xsl:template>
  <!-- tables -->
  <xsl:template match="table">
    <xsl:variable name="tbl-uid" select="./@xml:id" />
    <table class="content-table" name="{$tbl-uid}">
      <thead>
        <xsl:for-each select="head/cell">
          <xsl:variable name="head-uid" select="./@xml:id" />
          <th name="{$head-uid}">
            <xsl:apply-templates />
          </th>
        </xsl:for-each>
      </thead>
      <xsl:for-each select="row">
        <tr>
          <xsl:for-each select="cell">
            <xsl:variable name="cell-uid" select="./@xml:id" />
            <xsl:variable name="cellspan" select="./@span" />
            <xsl:variable name="border" select="./@border" />
            <td colspan="{$cellspan}" class="{$border}" name="{$cell-uid}">
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
  <xsl:template match="summary"></xsl:template>

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
            <xsl:variable name="title-uid" select="./@xml:id" />
            <th name="{$title-uid}"><xsl:value-of select="@name" /></th>
          </xsl:if>
          <xsl:if test="./@table">
            <xsl:variable name="table-uid" select="./@xml:id" />
            <th class="content" name="{$table-uid}"><xsl:value-of select="@name" /></th>
          </xsl:if>
        </xsl:for-each>
      </thead>
      <!-- Build the body of the table from the datums found -->
      <xsl:for-each select="//category[@name=$hrid]/datum">
        <tr>
          <xsl:for-each select="field" >
            <xsl:if test="./@title">
              <xsl:variable name="bdtt-uid" select="./@xml:id" />
              <td>
                <a href="#{generate-id(.)}" name="{$bdtt-uid}">
                  <xsl:apply-templates />
                </a>
              </td>
            </xsl:if>
            <xsl:if test="./@table">
              <xsl:variable name="body-uid" select="./@xml:id" />
              <td class="content" name="{$body-uid}"><xsl:apply-templates select="." /></td>
            </xsl:if>
          </xsl:for-each>
        </tr>
      </xsl:for-each>
    </table>
    <!-- Builds the descriptor lists -->
    <xsl:for-each select="//category[@name=$hrid]/datum">
      <xsl:variable name="datum-uid" select="./@xml:id" />
      <xsl:variable name="datum-title" select="field[@title='yes']" />
      <xsl:variable name="title-uid" select="field[@title='yes']/@xml:id" />
      <xsl:variable name="title-id" select="generate-id(field)"/>
      <xsl:variable name="desc-uid" select="field[@description='yes']/@xml:id" />
      <div class="datum-description"
        id="{$title-id}" name="{$datum-uid}">
        <span class="datum-description" name="{$title-uid}">
          <xsl:value-of select="field[@title='yes']" />
        </span>
        <p name="{$desc-uid}"><xsl:value-of select="field[@description='yes']" /></p>
      </div>
    </xsl:for-each>
  </xsl:template>

  <!-- math -->
  <xsl:template match="equation">
    <xsl:variable name="eqn-uid" select="./@xml:id" />
    <div class="math-equation" name="{$eqn-uid}">
      <xsl:apply-templates />
    </div>
  </xsl:template>
  <xsl:template match="math">{math <xsl:apply-templates />}</xsl:template>
  <xsl:template match="mrow">(<xsl:apply-templates />)</xsl:template>
  <xsl:template match="mi"><xsl:apply-templates /></xsl:template>
  <xsl:template match="mo"><xsl:apply-templates /></xsl:template>
  <xsl:template match="mn"><xsl:apply-templates /></xsl:template>
  <xsl:template match="msup">^(<xsl:apply-templates />)</xsl:template>
  <xsl:template match="munderover">{- <xsl:apply-templates />}</xsl:template>
  <xsl:template match="mfrac">{/ <xsl:apply-templates />}</xsl:template>
  <xsl:template match="mstyle">{style <xsl:apply-templates />}</xsl:template>

</xsl:stylesheet>