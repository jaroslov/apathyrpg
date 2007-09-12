<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output version="1.0"
    method="xml"
    encoding="ISO-8859-1"
    media-type="text/xml"
    indent="yes"/>
  <xsl:preserve-space elements="" />
  <xsl:strip-space elements=""/><!--description caption title field" />-->

  <xsl:template match="/">
    <apathy-game>
      <xsl:apply-templates select="apathy-game" />
    </apathy-game>
  </xsl:template>

  <xsl:template match="apathy-game">
    <book>
      <xsl:apply-templates select="book" />
    </book>
    <raw-data>
      <xsl:apply-templates select="raw-data" />
    </raw-data>
  </xsl:template>

  <!-- STRUCTURAL -->
  <xsl:template match="book">
    <xsl:apply-templates select="part" />
  </xsl:template>
  <xsl:template match="part">
    <part>
      <xsl:apply-templates select="title" />
      <xsl:apply-templates select="chapter" />
    </part>
  </xsl:template>
  <xsl:template match="chapter">
    <chapter>
      <xsl:apply-templates select="title" />
      <xsl:apply-templates select="section|reference" />
    </chapter>
  </xsl:template>
  <xsl:template match="section">
    <section>
      <xsl:apply-templates select="section|reference|title|text|example|description-list|itemized-list|numbered-list|figure|equation|note"/>
    </section>
  </xsl:template>
  <xsl:template match="raw-data">
    <xsl:apply-templates select="category"/>
  </xsl:template>

  <!-- LISTS -->
  <xsl:template match="itemized-list">
    <itemized-list>
      <xsl:apply-templates select="item"/>
    </itemized-list>
  </xsl:template>
  <xsl:template match="description-list">
    <description-list>
      <xsl:apply-templates select="item"/>
    </description-list>
  </xsl:template>
  <xsl:template match="numbered-list">
    <numbered-list>
      <xsl:apply-templates select="item"/>
    </numbered-list>
  </xsl:template>
  <xsl:template match="item">
    <item>
      <xsl:apply-templates select="description|text|text()|description-list|numbered-list|itemized-list|figure|equation|example|note" />
    </item>
  </xsl:template>
  <xsl:template match="description">
    <description><xsl:apply-templates /></description>
  </xsl:template>

  <!-- NONSTRUCTURAL BLOCK -->
  <xsl:template match="title">
    <title><xsl:apply-templates /></title>
  </xsl:template>
  <xsl:template match="text">
    <text>
      <xsl:apply-templates />
    </text>
  </xsl:template>
  <xsl:template match="note">
    <note>
      <xsl:apply-templates />
    </note>
  </xsl:template>
  <xsl:template match="example">
    <example>
      <xsl:apply-templates />
    </example>
  </xsl:template>
  <xsl:template match="footnote">
    <footnote>
      <xsl:apply-templates />
    </footnote>
  </xsl:template>

  <!-- INLINE -->
  <xsl:template match="text()">
    <xsl:variable name="text" select="." />
    <xsl:variable name="parent" select="name(parent::node())" />
    <xsl:choose>
      <!-- If you have a problem child... fix here -->
      <xsl:when test="$parent=''">
        <text><xsl:value-of select="normalize-space($text)" /></text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="normalize-space($text)" />
      </xsl:otherwise>  
    </xsl:choose>
  </xsl:template>
  <xsl:template match="reference">
    <xsl:variable name="hrid" select="./@hrid" />
    <reference hrid='{$hrid}' />
  </xsl:template>
  <xsl:template match="Apathy">
    <Apathy />
  </xsl:template>
  <xsl:template match="and">
    <and />
  </xsl:template>
  <xsl:template match="plusminus">
    <plusminus />
  </xsl:template>
  <xsl:template match="dollar">
    <dollar />
  </xsl:template>
  <xsl:template match="percent">
    <percent />
  </xsl:template>
  <xsl:template match="rightarrow">
    <rightarrow />
  </xsl:template>
  <xsl:template match="ldquo">
    <ldquo />
  </xsl:template>
  <xsl:template match="rdquo">
    <rdquo />
  </xsl:template>
  <xsl:template match="lsquo">
    <lsquo />
  </xsl:template>
  <xsl:template match="rsquo">
    <rsquo />
  </xsl:template>
  <xsl:template match="times">
    <times />
  </xsl:template>
  <xsl:template match="ouml">
    <ouml />
  </xsl:template>
  <xsl:template match="oslash">
    <oslash />
  </xsl:template>
  <xsl:template match="ndash">
    <ndash />
  </xsl:template>
  <xsl:template match="mdash">
    <mdash />
  </xsl:template>
  <xsl:template match="trademark">
    <trademark />
  </xsl:template>
  <xsl:template match="Sum">
    <Sum />
  </xsl:template>
  <xsl:template match="notappl">
    <notappl />
  </xsl:template>
  <xsl:template match="roll">
    <roll><xsl:apply-templates select="num|face|bOff|bns|mul|kind|rOff|raw" /></roll>
  </xsl:template>
  <xsl:template match="num">
    <num><xsl:apply-templates /></num>
  </xsl:template>
  <xsl:template match="face">
    <face><xsl:apply-templates /></face>
  </xsl:template>
  <xsl:template match="bOff">
    <bOff><xsl:apply-templates /></bOff>
  </xsl:template>
  <xsl:template match="bns">
    <bns><xsl:apply-templates /></bns>
  </xsl:template>
  <xsl:template match="mul">
    <mul><xsl:apply-templates /></mul>
  </xsl:template>
  <xsl:template match="kind">
    <kind><xsl:apply-templates /></kind>
  </xsl:template>
  <xsl:template match="rOff">
    <rOff><xsl:apply-templates /></rOff>
  </xsl:template>
  <xsl:template match="raw">
    <raw><xsl:apply-templates /></raw>
  </xsl:template>
  <xsl:template match="define">
    <define><xsl:apply-templates /></define>
  </xsl:template>

  <!-- FIGURE -->
  <xsl:template match="figure">
    <figure>
      <xsl:apply-templates select="table|caption" />
    </figure>
  </xsl:template>
  <xsl:template match="caption">
    <caption><xsl:apply-templates /></caption>
  </xsl:template>
  <xsl:template match="table">
    <table>
      <xsl:apply-templates select="head|row"/>
    </table>
  </xsl:template>
  <xsl:template match="head">
    <head>
      <xsl:apply-templates select="cell" />
    </head>
  </xsl:template>
  <xsl:template match="row">
    <row>
      <xsl:apply-templates select="cell" />
    </row>
  </xsl:template>
  <xsl:template match="cell">
    <xsl:variable name="cellspan" select="./@span" />
    <xsl:variable name="border" select="./@border" />
    <xsl:variable name="colfmt" select="./@colfmt" />
    <xsl:element name="cell">
      <xsl:if test="$cellspan">
        <xsl:attribute name="span"><xsl:value-of select="$cellspan" /></xsl:attribute>
      </xsl:if>
      <xsl:if test="$border">
        <xsl:attribute name="border"><xsl:value-of select="$border" /></xsl:attribute>
      </xsl:if>
      <xsl:if test="$colfmt">
        <xsl:attribute name="colfmt"><xsl:value-of select="$colfmt" /></xsl:attribute>
      </xsl:if>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <!-- MATH -->
  <xsl:template match="equation">
    <equation><xsl:apply-templates select="math"/></equation>
  </xsl:template>
  <xsl:template match="math">
    <math><xsl:apply-templates select="mrow|msup|mfrac|times|mstyle|Sum" /></math>
  </xsl:template>
  <xsl:template match="mrow">
    <mrow><xsl:apply-templates /></mrow>
  </xsl:template>
  <xsl:template match="mi">
    <mi><xsl:apply-templates /></mi>
  </xsl:template>
  <xsl:template match="mo">
    <mo><xsl:apply-templates /></mo>
  </xsl:template>
  <xsl:template match="mn">
    <mn><xsl:apply-templates /></mn>
  </xsl:template>
  <xsl:template match="msup">
    <msup><xsl:apply-templates /></msup>
  </xsl:template>
  <xsl:template match="munderover">
    <munderover><xsl:apply-templates /></munderover>
  </xsl:template>
  <xsl:template match="mfrac">
    <mfrac><xsl:apply-templates /></mfrac>
  </xsl:template>
  <xsl:template match="mstyle">
    <mstyle><xsl:apply-templates /></mstyle>
  </xsl:template>

  <!-- RAW DATA -->
  <xsl:template match="category">
    <xsl:variable name="Name" select="./@name" />
    <category name='{$Name}'>
      <xsl:apply-templates select="default|datum" />
    </category>
  </xsl:template>
  <xsl:template match="default">
    <xsl:variable name="Name" select="./@name" />
    <default name='{$Name}'>
      <xsl:apply-templates select="field"/>
    </default>
  </xsl:template>
  <xsl:template match="datum">
    <xsl:variable name="Name" select="./@name" />
    <datum name='{$Name}'>
      <xsl:apply-templates select="field"/>
    </datum>
  </xsl:template>
  <xsl:template match="field">
    <xsl:variable name="name" select="@name" />
    <xsl:variable name="title" select="./@title" />
    <xsl:variable name="table" select="./@table" />
    <xsl:variable name="description" select="./@description" />
    <xsl:variable name="qsummary" select="./@qsummary" />
    <xsl:variable name="colfmt" select="./@colfmt" />
    <xsl:element name="field">
      <xsl:attribute name="name">
        <xsl:value-of select="$name" />
      </xsl:attribute>
      <xsl:if test="./@title">
        <xsl:attribute name="title">
          <xsl:value-of select="$title" />
        </xsl:attribute>
      </xsl:if>
      <xsl:if test="./@table">
        <xsl:attribute name="table">
          <xsl:value-of select="$table" />
        </xsl:attribute>
      </xsl:if>
      <xsl:if test="./@description">
        <xsl:attribute name="description">
          <xsl:value-of select="$description" />
        </xsl:attribute>
      </xsl:if>
      <xsl:if test="./@qsummary">
        <xsl:attribute name="qsummary">
          <xsl:value-of select="$qsummary" />
        </xsl:attribute>
      </xsl:if>
      <xsl:if test="./@colfmt">
        <xsl:attribute name="colfmt">
          <xsl:value-of select="$colfmt" />
        </xsl:attribute>
      </xsl:if>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>