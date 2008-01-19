<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">

  <xsl:template match="/">
    <xsl:apply-templates />
  </xsl:template>

  <!-- book -->
  <xsl:template match="book">
    <xsl:element name="div">
      <xsl:attribute name="class">book</xsl:attribute>
      <xsl:apply-templates select="header"/>
      <xsl:apply-templates select="section"/>
    </xsl:element>
  </xsl:template>

  <!-- header of the book -->
  <xsl:template match="header">
    <xsl:element name="div">
      <xsl:attribute name="class">header</xsl:attribute>
      <xsl:apply-templates select="authors"/>
    </xsl:element>
  </xsl:template>

  <!-- authors -->
  <xsl:template match="authors">
    <xsl:element name="div">
      <xsl:attribute name="class">authors</xsl:attribute>
      <xsl:apply-templates select="author"/>
    </xsl:element>
  </xsl:template>

  <!-- authors -->
  <xsl:template match="author">
    <xsl:element name="div">
      <xsl:attribute name="class">author</xsl:attribute>
      <xsl:value-of select="."/>
    </xsl:element>
  </xsl:template>

  <!-- sections of the book -->
  <xsl:template match="section">
    <xsl:element name="div">
      <xsl:variable name="kind" select="./@kind"/>
      <xsl:attribute name="class"><xsl:value-of select="$kind"/></xsl:attribute>
      <xsl:apply-templates select="title"/>
      <xsl:element name="div">
        <xsl:attribute name="class">section-body</xsl:attribute>
        <xsl:apply-templates select="description-list|equation|example|figure|itemized-list|note|numbered-list|reference|section|summarize|table|text"/>
      </xsl:element>
    </xsl:element>
  </xsl:template>

  <!-- Category -->
  <xsl:template match="category">
    <xsl:variable name="hrid" select="./@name" />
    <xsl:element name="table">
      <xsl:attribute name="name"><xsl:value-of select="$hrid" /></xsl:attribute>
      <!-- titles head -->
      <xsl:apply-templates select="default">
        <xsl:with-param name="which">titles</xsl:with-param>
      </xsl:apply-templates>
      <xsl:apply-templates select="default">
        <xsl:with-param name="which">display</xsl:with-param>
      </xsl:apply-templates>
      <xsl:apply-templates select="default">
        <xsl:with-param name="which">format</xsl:with-param>
      </xsl:apply-templates>
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
    <p><xsl:apply-templates select="Apathy|footnote|text()|roll|math"/></p>
  </xsl:template>
  <xsl:template match="Apathy">
    <span class="Apathy"><xsl:value-of select="."/></span>
  </xsl:template>
  <xsl:template match="footnote">
    <div class="footnote"><xsl:value-of select="."/></div>
  </xsl:template>
  <!-- Math is broken -->
  <xsl:template match="math">
    <math>HERE</math>
  </xsl:template>

  <!-- non-text boxes -->
  <xsl:template match="title">
    <h1 class="title"><xsl:apply-templates select="text" /></h1>
  </xsl:template>
  <xsl:template match="equation">
    <div class="equation"><xsl:apply-templates select="text" /></div>
  </xsl:template>
  <xsl:template match="reference">
    <xsl:element name="div">
      <xsl:attribute name="class">reference</xsl:attribute>
      <xsl:attribute name="name"><xsl:value-of select="./@hrid"/></xsl:attribute>
    </xsl:element>
  </xsl:template>
  <xsl:template match="summarize">
    <xsl:element name="div">
      <xsl:attribute name="class">summarize</xsl:attribute>
      <xsl:attribute name="name"><xsl:value-of select="./@hrid"/></xsl:attribute>
    </xsl:element>
  </xsl:template>
  <xsl:template match="note">
    <div class="note">
      <xsl:apply-templates select="title" />
      <xsl:apply-templates select="text" />
    </div>
  </xsl:template>

  <!-- figure -->
  <xsl:template match="figure">
    <div class="figure"><xsl:apply-templates select="table|text" /></div>
  </xsl:template>
  <xsl:template match="table">
    <table class="display-table">
      <xsl:if test="../caption">
        <xsl:apply-templates select="../caption"/>
      </xsl:if>
      <xsl:apply-templates select="head"/>
      <tbody><xsl:apply-templates select="row"/></tbody>
    </table>
  </xsl:template>
  <xsl:template match="head">
    <thead><xsl:apply-templates select="cell"/></thead>
  </xsl:template>
  <xsl:template match="row">
    <tr><xsl:apply-templates select="cell"/></tr>
  </xsl:template>
  <xsl:template match="cell">
    <xsl:element name="td">
      <xsl:if test="./@span">
        <xsl:attribute name="colspan"><xsl:value-of select="./@span"/></xsl:attribute>
      </xsl:if>
      <xsl:apply-templates />
    </xsl:element>
  </xsl:template>
  <xsl:template match="caption">
    <caption><xsl:apply-templates /></caption>
  </xsl:template>

  <!-- lists -->
  <xsl:template match="itemized-list">
    <ul class="itemized-list">
      <xsl:apply-templates select="item"/>
    </ul>
  </xsl:template>
  <xsl:template match="numbered-list">
    <ol class="numbered-list">
      <xsl:apply-templates select="item"/>
    </ol>
  </xsl:template>
  <xsl:template match="description-list">
    <dl class="description-list">
      <xsl:for-each select="item">
        <xsl:apply-templates select="dt"/>
        <dd>
          <xsl:apply-templates select="description-list|equation|example|figure|itemized-list|note|numbered-list|reference|summarize|table|text"/>
        </dd>
      </xsl:for-each>
    </dl>
  </xsl:template>
  <xsl:template match="item">
    <li><xsl:apply-templates /></li>
  </xsl:template>
  <xsl:template match="description">
    <dt><xsl:apply-templates /></dt>
  </xsl:template>

  <!-- ROLL -->
  <xsl:template match="roll">
    <span class="span"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="bOff">
    <span class="bOff"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="bns">
    <span class="bns"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="face">
    <span class="face"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="num">
    <span class="num"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="rOff">
    <span class="rOff"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="raw">
    <span class="raw"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="mul">
    <span class="mul"><xsl:apply-templates /></span>
  </xsl:template>
  <xsl:template match="kind">
    <span class="kind"><xsl:apply-templates /></span>
  </xsl:template>


  <!-- thead -->
  <xsl:template match="default">
    <xsl:param name="which"/>
      <xsl:element name="thead">
        <xsl:attribute name="name"><xsl:value-of select="$which"/></xsl:attribute>
        <xsl:for-each select="./field">
          <xsl:variable name="FieldName" select="./@name" />
          <xsl:variable name="Title" select="./@title" />
          <xsl:variable name="ColFmt" select="./@colfmt" />
          <xsl:variable name="Table" select="./@table" />
          <xsl:variable name="Description" select="./@description" />
          <xsl:choose>
            <xsl:when test="$which = 'titles'">
              <xsl:element name="th">
                <xsl:value-of select="$FieldName"/>
              </xsl:element>
            </xsl:when>
            <xsl:when test="$which = 'display'">
              <xsl:element name="th">
                <xsl:choose>
                  <xsl:when test="$Title">Title</xsl:when>
                  <xsl:when test="$Description">Description</xsl:when>
                  <xsl:when test="$Table">Table</xsl:when>
                  <xsl:otherwise>None</xsl:otherwise>
                </xsl:choose>
              </xsl:element>
            </xsl:when>
            <xsl:when test="$which = 'format'">
              <xsl:element name="th">
                <xsl:value-of select="$ColFmt"/>
              </xsl:element>
            </xsl:when>
          </xsl:choose>
        </xsl:for-each>
      </xsl:element>

  </xsl:template>

</xsl:stylesheet>