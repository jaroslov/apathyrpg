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

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
      <head>
        <title>Apathy Manual (ARPG)</title>
        <link rel="stylesheet"
          type="text/css"
          href="apathy.css"
          title="Apathy" />
      </head>
      <body>
        <xsl:apply-templates select="xhtml:div" />
        <xsl:apply-templates select="xhtml:table">
          <xsl:with-param name="style">Display</xsl:with-param>
        </xsl:apply-templates>
      </body>
    </html>
  </xsl:template>

  <xsl:template match="xhtml:div">
    <!-- must be a 'book' -->
    <xsl:variable name="Class" select="./@class" />
    <xsl:if test="$Class='book'">
      <xsl:copy-of select="." />
    </xsl:if>
  </xsl:template>

  <xsl:template match="xhtml:table">
    <xsl:param name="style">Edit</xsl:param>
    <!-- must be a 'category' -->
    <xsl:variable name="Class" select="./@class" />
    
    <xsl:if test="$Class='category'">
      <!-- next two variables find the position (index)
            of the title and description nodes within the node-set
            of cells (th) -->
      <xsl:variable name="description-index"
        select="count(./xhtml:thead[@name='display']/xhtml:th[text()='Description']/preceding-sibling::xhtml:th)+1" />
      <xsl:variable name="title-index"
        select="count(./xhtml:thead[@name='display']/xhtml:th[text()='Title']/preceding-sibling::xhtml:th)+1" />
      <!-- display the "Edit" style -->
      <xsl:choose>
        <xsl:when test="$style='Edit'">
          <xsl:copy-of select="." />
        </xsl:when>
        <xsl:when test="$style='Display'">
          <!-- show table elements in table -->
          <xsl:variable name="name" select="./@name" />
          <xsl:variable name="id" select="generate-id(.)" />
          <xsl:element name="table"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
            <xsl:attribute name="class">category</xsl:attribute>
            <xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
            <xsl:element name="thead"
              namespace="http://www.w3.org/1999/xhtml">
              <xsl:for-each select="./xhtml:thead[@name='titles']/xhtml:th">
                <xsl:variable name="index" select="position()" />
                <xsl:variable name="display" select="../../xhtml:thead[@name='display']/xhtml:th[position()=$index]" />
                <xsl:choose>
                  <xsl:when test="$display='Title'">
                    <xsl:element name="th"
                      namespace="http://www.w3.org/1999/xhtml">
                      <xsl:value-of select="." />
                    </xsl:element>
                  </xsl:when>
                  <xsl:when test="$display='Table'">
                    <xsl:element name="th"
                      namespace="http://www.w3.org/1999/xhtml">
                      <xsl:value-of select="." />
                    </xsl:element>
                  </xsl:when>
                  <xsl:otherwise />
                </xsl:choose>
              </xsl:for-each>
            </xsl:element>
            <xsl:element name="tbody"
              namespace="http://www.w3.org/1999/xhtml">
              <!--
                1. loop through all the rows (tr) and rebuild as rows
                  2. loop through all the cells in the row (td) and rebuild
                    3. determine the index in the set of td of this cell ($index)
                    4. get the node containing the description ($description-node)
                    5. generate title with link
                    6. generate table-entries
              -->
              <xsl:for-each select="./xhtml:tbody/xhtml:tr">
                <xsl:element name="tr"
                  namespace="http://www.w3.org/1999/xhtml">
                  <xsl:for-each select="xhtml:td">
                    <xsl:variable name="index" select="position()" />
                    <xsl:variable name="description-node"
                      select="../xhtml:td[position()=$description-index]"/>
                    <xsl:variable name="display"
                      select="../../../xhtml:thead[@name='display']/xhtml:th[position()=$index]" />
                    <xsl:choose>
                      <xsl:when test="$display='Title'">
                        <xsl:element name="td"
                          namespace="http://www.w3.org/1999/xhtml">
                          <xsl:variable name="title-id" select="generate-id(.)"/>
                          <xsl:element name="a"
                            namespace="http://www.w3.org/1999/xhtml">
                            <xsl:attribute name="href">#<xsl:value-of select="$title-id"/></xsl:attribute>
                            <xsl:value-of select="." />
                          </xsl:element>
                        </xsl:element>
                      </xsl:when>
                      <xsl:when test="$display='Table'">
                        <xsl:element name="td"
                          namespace="http://www.w3.org/1999/xhtml">
                          <xsl:value-of select="." />
                        </xsl:element>
                      </xsl:when>
                      <xsl:otherwise />
                    </xsl:choose>
                  </xsl:for-each>
                </xsl:element>
              </xsl:for-each>
            </xsl:element>
          </xsl:element>
          <!-- show description -->
          <xsl:element name="div"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="class">category</xsl:attribute>
            <xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
            <xsl:for-each select="xhtml:tbody/xhtml:tr">
              <xsl:variable name="title-node" select="./xhtml:td[position()=$title-index]"/>
              <xsl:element name="div"
                namespace="http://www.w3.org/1999/xhtml">
                <xsl:choose>
                  <xsl:when test="./xhtml:td[position()=$title-index]">
                    <xsl:element name="h1"
                      namespace="http://www.w3.org/1999/xhtml">
                      <xsl:attribute name="id"><xsl:value-of select="generate-id($title-node)"/></xsl:attribute>
                      <xsl:value-of select="./xhtml:td[position()=$title-index]"/>
                    </xsl:element>
                  </xsl:when>
                  <xsl:otherwise />
                </xsl:choose>
                <xsl:element name="div"
                  namespace="http://www.w3.org/1999/xhtml">
                  <xsl:attribute name="id">description</xsl:attribute>
                  <xsl:value-of select="./xhtml:td[position()=$description-index]" />
                </xsl:element>
              </xsl:element>
            </xsl:for-each>
          </xsl:element>
        </xsl:when>
        <xsl:otherwise />
      </xsl:choose>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>