<?xml version="1.0" encoding="ISO-8859-1" ?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/">
    <html>
      <head>
        <style>
          body {
            font-size: 12pt;
          }
          p.text-description {
            padding-left: 2em;
            padding-right: 1em;
            text-indent: -1em;
            text-align: justify;
          }
          div.text-description {
            border: 1px solid black;
            width: 35em;
            margin: .5em;
            padding: .5em;
          }
          thead td {
            font-weight: bold;
          }
          td {
            border-bottom: 1px solid black;
            padding-right: .5em;
          }
          td.text-description-td-body {
            border-left: 1px dotted black;
            padding-left: .5em;
            padding-right: .5em;
          }
          tr:hover {
            background-color: #DDF;
          }
          table.qsummary tr:hover {
            background-color: #FFF;
          }
          table {
            border-collapse: collapse;
          }
          table.qsummary td {
            border: 0;
          }
          td.qsummary-field-name {
            font-variant: small-caps;
            font-size: 11pt;
            font-weight: bold;
            text-align: right;
          }
          td.qsummary-field-value {
            font-size: 11pt;
            text-align: left;
          }
          a {
            font-variant: small-caps;
            font-style: italic;
            text-decoration: none;
            color: black;
          }
          a:hover {
            font-style: italic;
            text-decoration: underline;
          }
        </style>
      </head>
      <body>
        <xsl:apply-templates />
      </body>
    </html>
  </xsl:template>

  <xsl:template match="category">
    <div id="{../../@name}-{../@name}-{@name}">
      <h1><xsl:value-of select="@name" /></h1>
      <table>
        <xsl:apply-templates select="default" />
        <xsl:for-each select="category">
          <a href="#{../../@name}-{../@name}-{@name}">
            <xsl:value-of select="@name" />
          </a><br />
        </xsl:for-each>
        <xsl:for-each select="datum">
          <tr>
            <xsl:for-each select="field">
              <xsl:choose>
                <xsl:when test="@title">
                  <td><a href="#{../../../@name}-{../../@name}-{../@name}">
                    <xsl:value-of select="." />
                  </a></td>
                </xsl:when>
                <xsl:when test="@table">
                  <td class="text-description-td-body">
                    <xsl:value-of select="." />
                  </td>
                </xsl:when>
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
          <td class="text-description-td-body"><xsl:value-of select="@name" /></td>
        </xsl:when>
        <xsl:when test="@title">
          <td><xsl:value-of select="@name" /></td>
        </xsl:when>
      </xsl:choose>
    </xsl:for-each>
    </tr></thead>
  </xsl:template>

  <xsl:template match="datum">
    <div class="text-description" id="{../../@name}-{../@name}-{@name}">
      <h2>
        <xsl:value-of select="@name" />
      </h2>
      <table class="qsummary" >
        <xsl:for-each select="field">
          <xsl:if test="@qsummary">
            <tr>
              <td class="qsummary-field-name"><xsl:value-of select="@name" />:</td>
              <td class="qsummary-field-value"><xsl:value-of select="." /></td>
            </tr>
          </xsl:if>
        </xsl:for-each>
      </table>
      <p>
        <xsl:for-each select="field">
          <xsl:choose>
            <xsl:when test="@name='Description'">
              <p class="text-description"><xsl:value-of select="." /></p>
            </xsl:when>
          </xsl:choose>
        </xsl:for-each>
      </p>
    </div>
  </xsl:template>

</xsl:stylesheet>
