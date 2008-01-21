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
    <xsl:if test="$Class='book'">
      <xsl:choose>
        <xsl:when test="$combine='No'">
          <xsl:copy-of select="." />
        </xsl:when>
        <xsl:when test="$combine='Yes'">
          <xsl:value-of select="$suffix" />
        </xsl:when>
        <xsl:otherwise/>
      </xsl:choose>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>