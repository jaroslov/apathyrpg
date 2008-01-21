<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml">

  <xsl:template match="xhtml:div">
    <xsl:param name="combine">No</xsl:param>
    <xsl:param name="suffix">.xhtml</xsl:param>
    <!-- must be a 'book' -->
    <xsl:variable name="Class" select="./@class" />
    <xsl:if test="$combine='Yes'">
      <!-- All the different divs
        book
          header
            author
          part
            title
            section-body
              chapter
                title
                section-body
                  section
                    title
                    section-body
                      section
      -->
      <xsl:choose>
        <xsl:when test="$Class='book'">
          <xsl:choose>
            <xsl:when test="$combine='No'">
              <xsl:copy-of select="." />
            </xsl:when>
            <xsl:when test="$combine='Yes'">
              <xsl:element name="div"
                namespace="http://www.w3.org/1999/xhtml">
                <xsl:attribute name="class">book</xsl:attribute>
                <xsl:attribute name="name">book</xsl:attribute>
                <xsl:apply-templates select="xhtml:div">
                  <xsl:with-param name="combine">
                    <xsl:value-of select="$combine"/>
                  </xsl:with-param>
                  <xsl:with-param name="suffix">
                    <xsl:value-of select="$combine"/>
                  </xsl:with-param>
                </xsl:apply-templates>
              </xsl:element>
            </xsl:when>
            <xsl:otherwise/>
          </xsl:choose>
        </xsl:when>
        <xsl:when test="($Class='header') or ($Class='part') or ($Class='chapter') or ($Class='section') or ($Class='authors') or ($Class='author') or ($Class='section-body')">
          <xsl:element name="div"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="class"><xsl:value-of select="$Class"/></xsl:attribute>
            <xsl:attribute name="name"><xsl:value-of select="$Class"/></xsl:attribute>
            <xsl:choose>
              <xsl:when test="$Class='author'">
                <xsl:copy-of select="./node()|./text()" />
              </xsl:when>
              <xsl:when test="($Class='part') or ($Class='chapter') or ($Class='section')">
                <xsl:apply-templates select="xhtml:h1"/>
                <xsl:apply-templates select="xhtml:div">
                  <xsl:with-param name="combine">
                    <xsl:value-of select="$combine"/>
                  </xsl:with-param>
                  <xsl:with-param name="suffix">
                    <xsl:value-of select="$combine"/>
                  </xsl:with-param>
                </xsl:apply-templates>
              </xsl:when>
              <xsl:otherwise>
                <xsl:apply-templates select="xhtml:div|xhtml:p|xhtml:Apathy|xhtml:ol|xhtml:ul|xhtml:dl">
                  <xsl:with-param name="combine">
                    <xsl:value-of select="$combine"/>
                  </xsl:with-param>
                  <xsl:with-param name="suffix">
                    <xsl:value-of select="$combine"/>
                  </xsl:with-param>
                </xsl:apply-templates>
              </xsl:otherwise>
            </xsl:choose>
          </xsl:element>
        </xsl:when>
        <xsl:when test="$Class='figure'">
          <xsl:element name="div"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="class">figure</xsl:attribute>
            <xsl:apply-templates select="xhtml:table" />
          </xsl:element>
        </xsl:when>
        <xsl:when test="$Class='note'">
          <xsl:element name="div"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="class">note</xsl:attribute>
            <xsl:apply-templates/>
          </xsl:element>
        </xsl:when>
        <xsl:otherwise>
          <xsl:element name="div"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="class">error</xsl:attribute>
            I don&apos;t know: <xsl:value-of select="$Class"/>
          </xsl:element>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:if>
  </xsl:template>

  <xsl:template match="xhtml:h1">
    <xsl:element name="h1"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:p">
    <xsl:element name="p"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:copy-of select="./*|./text()"/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:Apathy">
    <xsl:element name="Apathy"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:table">
    <xsl:element name="table"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
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
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:th">
    <xsl:element name="th"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:tbody">
    <xsl:element name="thead"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:tr">
    <xsl:element name="tr"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:td">
    <xsl:element name="td"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:ol">
    <xsl:element name="ol"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:ul">
    <xsl:element name="ul"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:li">
    <xsl:element name="li"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:dl">
    <xsl:element name="dl"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:dt">
    <xsl:element name="dt"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:dd">
    <xsl:element name="dd"
      namespace="http://www.w3.org/1999/xhtml">
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>