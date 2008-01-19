<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xlink="http://www.w3.org/1999/xlink"
  exclude-result-prefixes="xlink"
  xmlns="http://www.w3.org/1999/xhtml">

  <xsl:output version="1.0"
    method="xml"
    encoding="ISO-8859-1"
    media-type="text/html"
    indent="yes"
    doctype-public="-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN"
    doctype-system="http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd"/>

  <xsl:template match="/">
    <xsl:element name="html">
      <xsl:element name="head">
        <title>Apathy</title>
      </xsl:element>
      <xsl:element name="body">
        <xsl:apply-templates />
      </xsl:element>
    </xsl:element>

  </xsl:template>

  <!-- Category -->
  <xsl:template match="category">
    <xsl:variable name="hrid" select="./@name" />
    <xsl:element name="table">
      <xsl:attribute name="name"><xsl:value-of select="$hrid" /></xsl:attribute>
      <thead>
        <xsl:for-each select="default/field">
          <xsl:variable name="FieldName" select="./@name" />
          <xsl:variable name="Title" select="./@title" />
          <xsl:variable name="ColFmt" select="./@colfmt" />
          <xsl:variable name="Table" select="./@table" />
          <xsl:variable name="Description" select="./@description" />
          <xsl:element name="th">
            <xsl:if test="$Title">
              <xsl:attribute name="title"><xsl:value-of select="$Title"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="$ColFmt">
              <xsl:attribute name="colfmt"><xsl:value-of select="$ColFmt"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="$Table">
              <xsl:attribute name="table"><xsl:value-of select="$Table"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="$Description">
              <xsl:attribute name="description"><xsl:value-of select="$Description"/></xsl:attribute>
            </xsl:if>
            <xsl:value-of select="$FieldName" />
          </xsl:element>
        </xsl:for-each>
      </thead>
      <tbody>
        <xsl:for-each select="datum">
          <tr>
            <xsl:for-each select="field">
              <xsl:variable name="FieldName" select="./@name" />
              <xsl:variable name="Title" select="./@title" />
              <xsl:variable name="ColFmt" select="./@colfmt" />
              <xsl:variable name="Table" select="./@table" />
              <xsl:variable name="Description" select="./@description" />
              <xsl:element name="td">
                <xsl:if test="$Title">
                  <xsl:attribute name="title"><xsl:value-of select="$Title"/></xsl:attribute>
                </xsl:if>
                <xsl:if test="$ColFmt">
                  <xsl:attribute name="colfmt"><xsl:value-of select="$ColFmt"/></xsl:attribute>
                </xsl:if>
                <xsl:if test="$Table">
                  <xsl:attribute name="table"><xsl:value-of select="$Table"/></xsl:attribute>
                </xsl:if>
                <xsl:if test="$Description">
                  <xsl:attribute name="description"><xsl:value-of select="$Description"/></xsl:attribute>
                </xsl:if>
                <xsl:apply-templates select="." />
              </xsl:element>
            </xsl:for-each>
          </tr>
        </xsl:for-each>
      </tbody>
    </xsl:element>
  </xsl:template>

  <!-- TEXT ORIENTED -->
  <xsl:template match="text">
    <p><xsl:value-of select="."/></p>
  </xsl:template>

</xsl:stylesheet>