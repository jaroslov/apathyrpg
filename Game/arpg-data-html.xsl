<?xml version="1.0" encoding="ISO-8859-1" ?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/">
    <html>
      <head>
        <style>
          div {
            border: 1px solid black;
            padding: .1em;
            margin: .1em;
          }
        </style>
      </head>
      <body>
        <xsl:for-each select="*/category">
          <a href="#{@name}">
            <xsl:value-of select="@name" />
          </a><br />
        </xsl:for-each>
        <xsl:apply-templates />
      </body>
    </html>
  </xsl:template>

  <xsl:template match="category">
    <div id="{@name}">
      <h1><xsl:value-of select="@name" /></h1>
      <table>
        <xsl:apply-templates select="default" />
        <xsl:for-each select="datum">
          <tr>
            <xsl:for-each select="field">
              <xsl:choose>
                <xsl:when test="../../default/field[@name='Name']/@table">
                  <td><a href="#{../../@name}-{../@name}">
                    <xsl:value-of select="." />
                  </a></td>
                </xsl:when>
                <xsl:otherwise>
                  <td><a href="#{../../@name}-{../@name}">
                    <b>BAD</b><xsl:value-of select="../default/@table" />
                  </a></td>
                </xsl:otherwise>
              </xsl:choose>
            </xsl:for-each>
          </tr>
        </xsl:for-each>
      </table>
      <xsl:apply-templates select="category" />
      <xsl:apply-templates select="datum" />
    </div>
  </xsl:template>


  <xsl:template match="default">
    <thead><tr>
    <xsl:for-each select="field">
      <xsl:choose>
        <xsl:when test="@table">
          <td><xsl:value-of select="@name" /></td>
        </xsl:when>
      </xsl:choose>
    </xsl:for-each>
    </tr></thead>
  </xsl:template>

  <xsl:template match="datum">
    <h2 id="{../@name}-{@name}">
      <xsl:value-of select="@name" />
    </h2>
    <p>
      <xsl:for-each select="field">
        <xsl:choose>
          <xsl:when test="@name='Description'">
            <td><xsl:value-of select="." /></td>
          </xsl:when>
        </xsl:choose>
      </xsl:for-each>
    </p>
  </xsl:template>

</xsl:stylesheet>
