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

  <xsl:preserve-space elements="text" />
  <xsl:strip-space elements=""/><!--description caption title field" />-->

  <xsl:template match="/">
    <xsl:processing-instruction name="xml-stylesheet">href="xml-apathy.css" type="text/css"</xsl:processing-instruction>

    <xsl:element name="apathy-game">
      <xsl:apply-templates select="apathy-game" />
    </xsl:element>
  </xsl:template>

  <xsl:template match="apathy-game">
    <xsl:element name="book">
      <xsl:variable name="name" select="./@name" />
      <xsl:attribute name="name"><xsl:value-of select="$name" /></xsl:attribute>
      <xsl:apply-templates select="book" />
    </xsl:element>
  </xsl:template>

  <!-- STRUCTURAL -->
  <xsl:template match="book">
    <xsl:apply-templates select="section" />
  </xsl:template>
  <xsl:template match="section">
    <xsl:element name="section">
      <xsl:variable name="kind" select="./@kind" />
      <xsl:attribute name="kind"><xsl:value-of select="$kind" /></xsl:attribute>
      <xsl:apply-templates select="section|reference|summary|title|text|example|description-list|itemized-list|numbered-list|figure|footnote|equation|note"/>
    </xsl:element>
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
    <xsl:copy-of select="." />
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
    <xsl:copy-of select="." />
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
    <xsl:copy-of select="." />
  </xsl:template>
  <xsl:template match="math">
    <xsl:copy-of select="." />
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