<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml">

  <xsl:output version="1.0"
    method="xml"
    encoding="ISO-8859-1"
    media-type="text/xhtml"
    indent="yes"
    doctype-public="-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN"
    doctype-system="http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd"/>

  <xsl:include href="Category.xsl"/>
  <xsl:include href="Document.xsl"/>

  <xsl:template match="/">
    <xsl:apply-templates select="xhtml:div">
      <xsl:with-param name="combine">Yes</xsl:with-param>
      <xsl:with-param name="suffix">.combine.xhtml</xsl:with-param>
    </xsl:apply-templates>
  </xsl:template>

</xsl:stylesheet>