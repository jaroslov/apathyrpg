<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/xhtml">

  <xsl:output version="1.0"
    method="xml"
    encoding="ISO-8859-1"
    media-type="text/html"
    indent="yes"
    doctype-public="-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN"
    doctype-system="http://www.w3.org/TR/MathML2/dtd/xhtml-math11-f.dtd"/>

  <xsl:template match="/">
    <html>
      <head>
        <title>Apathy Document</title>
        <link rel="stylesheet" type="text/css" href="apathy.css" title="HTML" />
        <link rel="stylesheet" type="text/css" href="apathy.css" title="Print" />
      </head>
      <body>
        <xsl:apply-templates />
      </body>
    </html>
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
      <xsl:apply-templates select="text()|resource" />
    </xsl:element>
  </xsl:template>

  <!-- img -->
  <xsl:template match="resource">
    <xsl:element name="img">
      <xsl:attribute name="width"><xsl:value-of select="./@width"/></xsl:attribute>
      <xsl:attribute name="src"><xsl:value-of select="./@location"/></xsl:attribute>
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

  <!-- stupid sigils I implemented and now hate -->
  <xsl:template match="and">&amp;</xsl:template>
  <xsl:template match="dollar">$</xsl:template>
  <xsl:template match="ldquo">&#8220;</xsl:template>
  <xsl:template match="lsquo">&#8216;</xsl:template>
  <xsl:template match="mdash">&#8212;</xsl:template>
  <xsl:template match="ndash">&#8211;</xsl:template>
  <xsl:template match="oslash">&#248;</xsl:template>
  <xsl:template match="ouml">&#246;</xsl:template>
  <xsl:template match="plusminus">&#177;</xsl:template>
  <xsl:template match="percent">%</xsl:template>
  <xsl:template match="rdquo">&#8221;</xsl:template>
  <xsl:template match="rightarrow">&#8594;</xsl:template>
  <xsl:template match="rsquo">&#8217;</xsl:template>
  <xsl:template match="Sum">&#8721;</xsl:template>
  <xsl:template match="times">&#215;</xsl:template>
  <xsl:template match="trademark">&#8482;</xsl:template>

  <!-- TEXT ORIENTED -->
  <xsl:template match="text">
    <p><xsl:apply-templates select="./*|text()"/></p>
  </xsl:template>
  <xsl:template match="Apathy">
    <span class="Apathy">ApAthy</span><xsl:apply-templates />
  </xsl:template>
  <xsl:template match="notappl">
    <span class="notappl">n/a</span>
  </xsl:template>
  <xsl:template match="footnote">
    <div class="footnote"><xsl:value-of select="."/></div>
  </xsl:template>
  <xsl:template match="example">
    <div class="example">
      <xsl:apply-templates />
    </div>
  </xsl:template>
  <xsl:template match="math">
    <xsl:element name="math" namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:if test="./@display">
        <xsl:attribute name="display"><xsl:value-of select="./@display"/></xsl:attribute>
      </xsl:if>
      <xsl:apply-templates select="./*"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="define">
    <span class="define"><xsl:value-of select="."/></span>
  </xsl:template>

  <!-- math -->
  <xsl:template match="mrow">
    <xsl:element name="mrow"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates select="./*"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="munderover">
    <xsl:element name="munderover"
      namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates select="./*"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="mi">
    <xsl:element name="mi" namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates select="./*|text()"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="mo">
    <xsl:element name="mo" namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates select="./*|text()"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="mn">
    <xsl:element name="mn" namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates select="./*|text()"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="mfrac">
    <xsl:element name="mfrac" namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates select="./*"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="msup">
    <xsl:element name="msup" namespace="http://www.w3.org/1998/Math/MathML">
      <xsl:apply-templates select="./*"/>
    </xsl:element>
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
      <xsl:variable name="hrid" select="./@hrid"/>
      <xsl:attribute name="name"><xsl:value-of select="$hrid"/></xsl:attribute>
      <xsl:element name="a">
        <xsl:attribute name="href"><xsl:value-of select="concat($hrid,'.xhtml')"/></xsl:attribute>
        <xsl:value-of select="$hrid"/>
      </xsl:element>
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
      <xsl:apply-templates />
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
    <thead>
      <tr>
        <xsl:apply-templates select="cell">
          <xsl:with-param name="kind">th</xsl:with-param>
        </xsl:apply-templates>
      </tr>
    </thead>
  </xsl:template>
  <xsl:template match="row">
    <tr>
      <xsl:apply-templates select="cell">
        <xsl:with-param name="kind">td</xsl:with-param>
      </xsl:apply-templates>
    </tr>
  </xsl:template>
  <xsl:template match="cell">
    <xsl:param name="kind">td</xsl:param>
    <xsl:element name="{$kind}">
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
        <xsl:apply-templates select="description"/>
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
    <dt><xsl:apply-templates select="text"/></dt>
  </xsl:template>

  <!-- ROLL -->
  <xsl:template match="roll">
    <span class="roll"><xsl:apply-templates /></span>
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