<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml">

  <xsl:template match="/">
    <xsl:apply-templates select="xhtml:div[@class='book']" />
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
      Finish up... need to find all the sub-types for section/section-body
      and make sure they're propogated to the *-lists; finish figure, table,
      note, example, et al.
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
    may contain text, only
  -->
  <xsl:template match="xhtml:h1[@class='title']">
    <xsl:element name="h1"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:attribute name="class">title</xsl:attribute>
      <xsl:copy-of select="xhtml:p" />
    </xsl:element>
  </xsl:template>

  <!--
    text (p)
      text() | footnote | Apathy | math
  -->
  <xsl:template match="xhtml:p">
    <xsl:element name="p"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:copy-of select="./*|text()" />
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
          <xsl:apply-templates select="xhtml:div[@class='section']|xhtml:p|xhtml:ol|xhtml:ul|xhtml:dl"/>
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