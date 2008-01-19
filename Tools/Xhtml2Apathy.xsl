<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/">
    <xsl:apply-templates select="//table" />
  </xsl:template>

  <xsl:template match="table">
    <xsl:variable name="Name" select="./@name" />
    <xsl:element name="category">
      <xsl:attribute name="name"><xsl:value-of select="$Name"/></xsl:attribute>
      <default>
        <xsl:for-each select="./thead/th">
          <xsl:variable name="FieldName" select="./@name" />
          <xsl:variable name="Title" select="./@title" />
          <xsl:variable name="ColFmt" select="./@colfmt" />
          <xsl:variable name="Table" select="./@table" />
          <xsl:variable name="Description" select="./@description" />
          <xsl:element name="field">
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
      </default>
      <xsl:for-each select="./tbody/tr">
        <xsl:element name="datum">
          <xsl:for-each select="td">
            <xsl:element name="field">
              <xsl:variable name="FieldName" select="./@name" />
              <xsl:variable name="Title" select="./@title" />
              <xsl:variable name="ColFmt" select="./@colfmt" />
              <xsl:variable name="Table" select="./@table" />
              <xsl:variable name="Description" select="./@description" />
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
        </xsl:element>
      </xsl:for-each>
    </xsl:element>
  </xsl:template>

  <!-- TEXT ORIENTED -->
  <xsl:template match="p">
    <text><xsl:value-of select="."/></text>
  </xsl:template>

</xsl:stylesheet>