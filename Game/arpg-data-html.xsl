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
	<xsl:for-each select="//category">
	  <a href=""><xsl:value-of select="@name" /></a><br />
	</xsl:for-each>
	<xsl:apply-templates />
      </body>
    </html>
  </xsl:template>

  <xsl:template match="category">
    <div>
      <h1><xsl:value-of select="@name" /></h1>
      <xsl:apply-templates select="category" />
      <xsl:apply-templates select="datum" />
    </div>
  </xsl:template>


  <xsl:template match="datum">
    <h2><xsl:value-of select="@name" /></h2>
    <table>
      <xsl:for-each select="field">
	<xsl:choose>
	  <xsl:when test="@name!='Implementation'">
	    <tr>
	      <td><xsl:value-of select="@name" /></td>
	      <td><xsl:value-of select="." /></td>
	    </tr>
	  </xsl:when>
	</xsl:choose>
      </xsl:for-each>
    </table>
  </xsl:template>

</xsl:stylesheet>
