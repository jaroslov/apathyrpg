<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
  xmlns:mathml="http://www.w3.org/1998/Math/MathML">

  <xsl:param name="media">Combine</xsl:param>
  <xsl:param name="title">Combine</xsl:param>

  <xsl:output version="1.0"
    method="xml"
    encoding="ISO-8859-1"
    media-type="text/xhtml"
    indent="yes"
    doctype-public="-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN"
    doctype-system="http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd"/>

  <xsl:template match="/">
    <xsl:choose>
      <xsl:when test="$media = 'xhtml'">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
          <head>
            <title><xsl:value-of select="$title"/></title>
              <!--Apathy Manual (ARPG)</title>-->
            <link rel="stylesheet"
              type="text/css"
              href="apathy.css"
              title="Apathy" />
          </head>
          <body>
            <xsl:apply-templates select="xhtml:div[@class='book']" />
          </body>
        </html>
      </xsl:when>
      <xsl:when test="$media = 'latex'">
        <xsl:apply-templates select="xhtml:div[@class='book']" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:apply-templates select="xhtml:div[@class='book']" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!--
    book
      header
      section-body
  -->
  <xsl:template match="xhtml:div[@class='book']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">book</xsl:attribute>
      <xsl:apply-templates select="xhtml:div[@class='header']"/>
      <xsl:apply-templates select="xhtml:div[@class='section-body']">
        <xsl:with-param name="parentKind">book</xsl:with-param>
      </xsl:apply-templates>
    </xsl:element>
  </xsl:template>

  <!--
    header
      authors
  -->
  <xsl:template match="xhtml:div[@class='header']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">header</xsl:attribute>
      <xsl:apply-templates select="xhtml:div[@class='authors']"/>
    </xsl:element>
  </xsl:template>

  <!--
    authors
      author
  -->
  <xsl:template match="xhtml:div[@class='authors']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">authors</xsl:attribute>
      <xsl:apply-templates select="xhtml:div[@class='author']"/>
    </xsl:element>
  </xsl:template>

  <!--
    author
      text()|img
  -->
  <xsl:template match="xhtml:div[@class='author']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">author</xsl:attribute>
      <xsl:copy-of select="xhtml:img|text()" />
    </xsl:element>
  </xsl:template>

  <!--
    part | chapter | section
      title
      section-body
  -->
  <xsl:template match="xhtml:div[@class='part']|xhtml:div[@class='chapter']|xhtml:div[@class='section']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class"><xsl:value-of select="./@class" /></xsl:attribute>
      <xsl:apply-templates select="xhtml:h1[@class='title']"/>
      <xsl:apply-templates select="xhtml:div[@class='section-body']">
        <xsl:with-param name="parentKind"><xsl:value-of select="./@class" /></xsl:with-param>
      </xsl:apply-templates>
    </xsl:element>
  </xsl:template>

  <!--
    equations have text
  -->
  <xsl:template match="xhtml:div[@class='equation']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">equation</xsl:attribute>
      <xsl:apply-templates select="xhtml:p"/>
    </xsl:element>
  </xsl:template>

  <!--
    notes have text
  -->
  <xsl:template match="xhtml:div[@class='note']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">note</xsl:attribute>
      <xsl:apply-templates select="xhtml:p"/>
    </xsl:element>
  </xsl:template>

  <!--
    reference
  -->
  <xsl:template match="xhtml:div[@class='reference']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">reference</xsl:attribute>
      <p><xsl:value-of select="./xhtml:a/@href"/></p>
    </xsl:element>
  </xsl:template>

  <!--
    equations have text
  -->
  <xsl:template match="xhtml:div[@class='example']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">example</xsl:attribute>
      <xsl:apply-templates select="xhtml:h1[@class='title']"/>
      <xsl:apply-templates select="xhtml:p"/>
    </xsl:element>
  </xsl:template>

  <!--
    figures have tables
  -->
  <xsl:template match="xhtml:div[@class='figure']">
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">figure</xsl:attribute>
      <xsl:apply-templates select="xhtml:table"/>
    </xsl:element>
  </xsl:template>

  <!--
    display-table
  -->
  <xsl:template match="xhtml:table[@class='display-table']">
    <xsl:element name="table"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">display-table</xsl:attribute>
      <xsl:apply-templates select="xhtml:caption"/>
      <xsl:apply-templates select="xhtml:thead"/>
      <xsl:apply-templates select="xhtml:tbody"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:caption">
    <xsl:element name="caption"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:thead">
    <xsl:element name="thead"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates select="xhtml:th"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:tbody">
    <xsl:element name="tbody"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates select="xhtml:tr"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:tr">
    <xsl:element name="tr"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates select="xhtml:td"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:th">
    <xsl:element name="th"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:if test="./@colspan">
        <xsl:attribute name="colspan"><xsl:value-of select="./@colspan"/></xsl:attribute>
      </xsl:if>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:td">
    <xsl:element name="td"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:if test="./@colspan">
        <xsl:attribute name="colspan"><xsl:value-of select="./@colspan"/></xsl:attribute>
      </xsl:if>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <!--
    math is copied whole-sale
  -->
  <xsl:template match="mathml:math">
    <xsl:copy-of select="." />
  </xsl:template>

  <!--
    may contain text, only
  -->
  <xsl:template match="xhtml:h1[@class='title']">
    <xsl:element name="h1"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">title</xsl:attribute>
      <xsl:apply-templates select="xhtml:p" />
    </xsl:element>
  </xsl:template>

  <!--
    text (p)
      text() | footnote | Apathy | math
  -->
  <xsl:template match="xhtml:p">
    <xsl:element name="p"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates select="xhtml:Apathy|text()|mathml:math|xhtml:span[@class='roll']|xhtml:span[@class='footnote']|xhtml:span[@class='notappl']" />
    </xsl:element>
  </xsl:template>

  <!--
    footnote
  -->
  <xsl:template match="xhtml:span[@class='footnote']">
    <xsl:element name="span"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">footnote</xsl:attribute>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <!--
    notappl
  -->
  <xsl:template match="xhtml:span[@class='notappl']">
    <xsl:element name="span"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">notappl</xsl:attribute>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <!--
    Apathy
  -->
  <xsl:template match="xhtml:Apathy">
    <xsl:element name="span"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">Apathy</xsl:attribute>
      ApAthy<xsl:copy-of select="text()" />
    </xsl:element>
  </xsl:template>

  <!--
    span:roll
  -->
  <xsl:template match="xhtml:span[@class='roll']">
    <xsl:element name="span"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">roll</xsl:attribute>
      <xsl:apply-templates select="xhtml:span[@class='rOff']" />
      <xsl:apply-templates select="xhtml:span[@class='raw']" />
      <xsl:apply-templates select="xhtml:span[@class='num']" />
      <xsl:apply-templates select="xhtml:span[@class='face']" />
      <xsl:apply-templates select="xhtml:span[@class='bOff']" />
      <xsl:apply-templates select="xhtml:span[@class='bns']" />
      <xsl:apply-templates select="xhtml:span[@class='mul']" />
      <xsl:apply-templates select="xhtml:span[@class='kind']" />
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:span[@class='raw']|xhtml:span[@class='rOff']|xhtml:span[@class='face']|xhtml:span[@class='num']|xhtml:span[@class='bOff']|xhtml:span[@class='bns']|xhtml:span[@class='mul']|xhtml:span[@class='kind']">
    <xsl:element name="span"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class"><xsl:value-of select="./@class"/></xsl:attribute>
      <xsl:value-of select="." />
    </xsl:element>
  </xsl:template>

  <!--
    ol | ul
      li
  -->
  <xsl:template match="xhtml:ol">
    <xsl:variable name="self" value="name(.)" />
    <xsl:element name="ol"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">numbered-list</xsl:attribute>
      <xsl:apply-templates select="xhtml:li" />
    </xsl:element>
  </xsl:template>
  <xsl:template match="xhtml:ul">
    <xsl:variable name="self" value="name(.)" />
    <xsl:element name="ul"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">itemized-list</xsl:attribute>
      <xsl:apply-templates select="xhtml:li" />
    </xsl:element>
  </xsl:template>

  <!--
    dl
      dt
      dd
  -->
  <xsl:template match="xhtml:dl">
    <xsl:element name="dl"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">description-list</xsl:attribute>
      <xsl:apply-templates select="xhtml:dt|xhtml:dd" />
    </xsl:element>
  </xsl:template>
  <!-- dt -> text -->
  <xsl:template match="xhtml:dt">
    <xsl:element name="dt"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates select="xhtml:p" />
    </xsl:element>
  </xsl:template>
  <!-- dd -> text -->
  <xsl:template match="xhtml:dd">
    <xsl:element name="dd"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates select="xhtml:p" />
    </xsl:element>
  </xsl:template>

  <!-- li -->
  <xsl:template match="xhtml:li">
    <xsl:element name="li"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates select="xhtml:p" />
    </xsl:element>
  </xsl:template>

  <!--
    section-body
      book | part | chapter | section
      s.t.
        book
          part
        part
          chapter
        section
          section | ...
  -->
  <xsl:template match="xhtml:div[@class='section-body']">
    <xsl:param name="parentKind">book</xsl:param>
    <xsl:element name="div"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">section-body</xsl:attribute>
      <xsl:choose>
        <xsl:when test="$parentKind='book'">
          <xsl:apply-templates select="xhtml:div[@class='part']"/>
        </xsl:when>
        <xsl:when test="$parentKind='part'">
          <xsl:apply-templates select="xhtml:div[@class='chapter']"/>
        </xsl:when>
        <xsl:when test="$parentKind='chapter'">
          <xsl:apply-templates select="xhtml:div[@class='section']"/>
        </xsl:when>
        <xsl:when test="$parentKind='section'">
          <xsl:apply-templates select="xhtml:div[@class='section']|xhtml:p|xhtml:ol|xhtml:ul|xhtml:dl|xhtml:div[@class='figure']|xhtml:div[@class='equation']|xhtml:div[@class='example']|xhtml:div[@class='note']|xhtml:div[@class='reference']"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:element name="div"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="class">error</xsl:attribute>
            Only &apos;book&apos;, &apos;part&apos;, &apos;chapter&apos;, and &apos;section&apos; may contain a section-body, you have a <xsl:value-of select="$parentKind"/>.
          </xsl:element>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>