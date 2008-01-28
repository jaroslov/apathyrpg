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
        <xsl:when test="$Class='reference'">
          <xsl:apply-templates select="document(./xhtml:a/@href)/xhtml:table">
            <xsl:with-param name="style">Display</xsl:with-param>
          </xsl:apply-templates>
          <xsl:apply-templates select="document(./xhtml:a/@href)/xhtml:table">
            <xsl:with-param name="style">Descriptions</xsl:with-param>
          </xsl:apply-templates>
        </xsl:when>
        <xsl:when test="$Class='summarize'">
          <xsl:apply-templates select="document(./xhtml:a/@href)/xhtml:table">
            <xsl:with-param name="style">Display</xsl:with-param>
          </xsl:apply-templates>
        </xsl:when>
        <xsl:when test="($Class='note') or ($Class='equation') or ($Class='example')">
          <xsl:element name="div"
            namespace="http://www.w3.org/1999/xhtml">
            <xsl:attribute name="class"><xsl:value-of select="$Class"/></xsl:attribute>
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
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>

  <xsl:template match="xhtml:Apathy">
    <xsl:element name="Apathy"
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

  <xsl:template match="xhtml:table">
    <xsl:param name="style">Edit</xsl:param>
    <!-- must be a 'category' -->
    <xsl:variable name="Class" select="./@class" />
    
    <xsl:choose>
      <xsl:when test="$Class='category'">
        <!-- next two variables find the position (index)
              of the title and description nodes within the node-set
              of cells (th) -->
        <xsl:variable name="description-index"
          select="count(./xhtml:thead[@name='display']/xhtml:th[text()='Description']/preceding-sibling::xhtml:th)+1" />
        <xsl:variable name="title-index"
          select="count(./xhtml:thead[@name='display']/xhtml:th[text()='Title']/preceding-sibling::xhtml:th)+1" />
        <xsl:variable name="name" select="./@name" />
        <xsl:variable name="id" select="generate-id(.)" />
        <!-- display the "Edit" style -->
        <xsl:choose>
          <xsl:when test="$style='Edit'">
            <xsl:copy-of select="." />
          </xsl:when>
          <xsl:when test="$style='Display'">
            <!-- show table elements in table -->
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
          </xsl:when>
          <xsl:when test="$style='Descriptions'">
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
                    <xsl:attribute name="class">description</xsl:attribute>
                    <xsl:value-of select="./xhtml:td[position()=$description-index]" />
                  </xsl:element>
                </xsl:element>
              </xsl:for-each>
            </xsl:element>
          </xsl:when>
          <xsl:otherwise />
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:element name="table"
          namespace="http://www.w3.org/1999/xhtml">
          <xsl:apply-templates />
        </xsl:element>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:stylesheet>