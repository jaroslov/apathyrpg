<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="text" encoding="UTF-8" media-type="text/plain" indent="no"/>
  <xsl:template match="/">
\documentclass[twoside]{book}
\usepackage{pslatex}
\usepackage{multicol}
\usepackage{newcent}
\usepackage{rotating}
\usepackage{tabularx}
\usepackage{array}
\usepackage{longtable}
\usepackage{multirow}
\usepackage{graphicx}
\usepackage[T1]{fontenc}
\usepackage{hyperref}
\usepackage{wrapfig}
\usepackage[text={6.5in,8in},textheight=8in]{geometry}

\begin{document}

%\renewcommand{\normalsize}{\fontsize{8.8pt}{10pt}\selectfont}
\newfont{\GIANT}{rpncr scaled 9500}
\newfont{\Giant}{rpncr scaled 4500}
\DeclareFixedFont{\apathyscbf}{OT1}{pnc}{b}{sc}{8}
\DeclareFixedFont{\rulescbf}{OT1}{pnc}{b}{sc}{11}
\DeclareFixedFont{\apathymn}{OT1}{pnc}{m}{n}{8}
\newcommand{\halfline}{\vspace{.5ex}}
\newcommand{\textscbf}[1]{\textsc{\textbf{#1}}}
\newcommand{\APATHY}{\textscbf{Apathy}}
\newcommand{\rulename}[1]
{
\noindent\rulescbf{#1}
}
\newcommand{\ruledesc}[1]
{
\parindent=5pt
\everypar{\hangindent=20pt \hangafter=1}
\normalsize #1
}
\newcommand{\ARPG}{
{\GIANT A}
\hspace{-3ex}\raise5ex\hbox{\Giant P}
\hspace{-4ex}{\GIANT A}
\hspace{-4ex}\raise5ex\hbox{\Giant THY}
\raise13ex\hbox{
\hspace{-23ex}\textscbf{R}\textsc{ole-}\textscbf{P}\textsc{laying}
\textscbf{G}\textsc{ame}
}
}
\newcounter{ExampleCounter}
\setcounter{ExampleCounter}{1}
\newcommand{\quotexample}[2][~]
{
\vspace{1em}
\addcontentsline{lof}{section}{\arabic{ExampleCounter} \textsc{#1}}
\vbox{
\textscbf{\noindent Example \arabic{ExampleCounter} {\small \textsc{#1}}}
\begin{quotation}
{\small #2}
\end{quotation}
\vspace{1em}
}
\addtocounter{ExampleCounter}{1}
}

\newcommand{\BracedWrapPicture}[2]
{
\begin{wrapfigure}{#1}{.40\textwidth}
  \vspace{-20pt}
  \begin{center}
    $\overbrace{\hspace{.40\textwidth}}$
    \vspace{-10pt}
    \includegraphics[width=0.38\textwidth]{#2}
    \vspace{-10pt}
    $\underbrace{\hspace{.40\textwidth}}$
  \end{center}
  \vspace{-15pt}
\end{wrapfigure}
}

\newcommand{\VBoxColumnPicture}[1]
{
\vbox{
\begin{center}
$\overbrace{\hspace{.8\columnwidth}}$
\vspace{-10pt}
\includegraphics[width=.76\columnwidth]{#1}
\vspace{-10pt}
$\underbrace{\hspace{.8\columnwidth}}$
\end{center}}
}


\begin{titlepage}
~\\~\\~\\~\\~\\~\\~\\~\\~\\~\\
\begin{center}
\includegraphics[width=\textwidth]{Apathy.png}\\[1cm]

\hspace*{0em}
\vbox{\vspace{-2em}
\small Allan Moyse \\
Nathan Jones \\
Jacob Smith \\
Noah Smith \\
Chris Cook \\
Josh Kramer}

\vskip 2in
\textsc{Revision \#429 (25 December 2007)}
\end{center}

\end{titlepage}

~
\setcounter{page}{1}
\pagenumbering{roman}
\setcounter{tocdepth}{3}
\tableofcontents
\newpage
\listoftables
\newpage
\listoffigures
\newpage
\pagenumbering{arabic}
\setcounter{page}{1}


  <xsl:apply-templates select="//book" />

\end{document}
  </xsl:template>
  
  <!-- STRUCTURAL ELEMENTS -->
  <!-- Book -->
  <xsl:template match="book">
    &#xa;
    <xsl:apply-templates select="section" />
  </xsl:template>
  <!-- Part -->
  <xsl:template match="part">
    &#xa;
\part{<xsl:apply-templates select="title" />}
    <xsl:apply-templates select="chapter|reference" />
  </xsl:template>
  <!-- Chapter -->
  <xsl:template match="chapter">
    &#xa;
\chapter{<xsl:apply-templates select="title" />}
    <xsl:apply-templates select="section|reference" />
  </xsl:template>
  <!-- Section -->
  <xsl:template match="section">
    <xsl:variable name="kind" select="./@kind" />
    <xsl:choose>
      <xsl:when test="$kind='part'">
        &#xa;
\part{<xsl:apply-templates select="title" />}
        <xsl:apply-templates select="section|reference" />
      </xsl:when>
      <xsl:when test="$kind='chapter'">
        &#xa;
\chapter{<xsl:apply-templates select="title" />}
      <xsl:apply-templates select="section|reference" />
      </xsl:when>
      <xsl:otherwise>
          &#xa;
\<xsl:if test="../@kind='section'">sub</xsl:if><xsl:if test="../../@kind='section'">sub</xsl:if><xsl:if test="../../../@kind='section'">sub</xsl:if>section{<xsl:apply-templates select="title" />}
        <xsl:if test="not(./section)">
          <!-- pBefore -->
        </xsl:if>
        <xsl:apply-templates select="./*[position()&gt;1]" />
        <xsl:if test="not(./section)">
          <!-- pAfter -->
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>


  <!-- INLINE ELEMENTS -->
  <xsl:template match="title">
    <xsl:apply-templates />
  </xsl:template>
  <!-- Generic Text -->
  <xsl:template match="text">{<xsl:apply-templates />}</xsl:template>
  <!--<xsl:template match="text()"><xsl:variable name="text" select="." /><xsl:value-of select="normalize-space($text)" /></xsl:template>-->
  <!-- Note -->
  <xsl:template match="note">&#xa;&#xa;\textscbf{Note!} \textbf{<xsl:apply-templates />}&#xa;&#xa;</xsl:template>
  <!-- examples -->
  <xsl:template match="example">
  &#xa;
\quotexample[<xsl:apply-templates select="title" />]{<xsl:apply-templates select="text" />}
  &#xa;
  </xsl:template>
  <!-- Apathy -->
  <xsl:template match="Apathy">\APATHY{}</xsl:template>
  <!-- C -->
  <xsl:template match="C">\textsc{C}</xsl:template>
  <!-- and -->
  <xsl:template match="and">\&amp;</xsl:template>
  <!-- special "dollar" word -->
  <xsl:template match="dollar">\$</xsl:template>
  <!-- special "percent" word -->
  <xsl:template match="percent">\%</xsl:template>
  <!-- special "rightarrow" word -->
  <xsl:template match="rightarrow">$\rightarrow$</xsl:template>
  <!-- special "ldquo" word -->
  <xsl:template match="ldquo">``</xsl:template>
  <!-- special "rdquo" word -->
  <xsl:template match="rdquo">''</xsl:template>
  <!-- special "lsquo" word -->
  <xsl:template match="lsquo">`</xsl:template>
  <!-- special "rsquo" word -->
  <xsl:template match="rsquo">'</xsl:template>
  <!-- special "times" word -->
  <xsl:template match="times">\times </xsl:template>
  <!-- special "ouml" word -->
  <xsl:template match="ouml">\"o</xsl:template>
  <!-- special "oslash" word -->
  <xsl:template match="oslash">o</xsl:template>
  <!-- special "ndash" word -->
  <xsl:template match="ndash">--</xsl:template>
  <!-- special "mdash" word -->
  <xsl:template match="mdash">---</xsl:template>
  <!-- special "trademark" word -->
  <xsl:template match="trademark">$^{TM}$</xsl:template>
  <!-- special "plusminus" word -->
  <xsl:template match="plusminus">\ensuremath{\pm}</xsl:template>
  <!-- mathematical sum -->
  <xsl:template match="Sum">\displaystyle\sum</xsl:template>
  <!-- "n/a" notappl -->
  <xsl:template match="notappl">\textit{n/a}</xsl:template>
  <!-- define -->
  <xsl:template match="define">\emph{<xsl:apply-templates />}</xsl:template>
  <!-- footnote -->
  <xsl:template match="footnote">\footnote{<xsl:apply-templates />}</xsl:template>
  <!-- dice rolls -->
  <xsl:template match="roll">
    <xsl:choose>
      <xsl:when test="./@type='alt'">
<xsl:apply-templates select="num"/><xsl:apply-templates select="face"/><xsl:apply-templates select="bOff"/><xsl:apply-templates select="bns"/><xsl:apply-templates select="mul"/><xsl:apply-templates select="kind"/><xsl:if test="raw">[\ensuremath{<xsl:apply-templates select="rOff"/><xsl:apply-templates select="raw"/>}]</xsl:if>
      </xsl:when>
      <xsl:otherwise>
<xsl:apply-templates select="rOff"/><xsl:apply-templates select="raw"/><xsl:if test="raw">\texttt{+}</xsl:if><xsl:apply-templates select="num"/><xsl:apply-templates select="face"/><xsl:apply-templates select="bOff"/><xsl:apply-templates select="bns"/><xsl:apply-templates select="mul"/><xsl:apply-templates select="kind"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <xsl:template match="rOff">\texttt{<xsl:apply-templates />}</xsl:template>
  <xsl:template match="raw">\ensuremath{<xsl:apply-templates />}</xsl:template>
  <xsl:template match="num">\ensuremath{<xsl:apply-templates />}</xsl:template>
  <xsl:template match="face">\textscbf{d}\ensuremath{<xsl:apply-templates />}</xsl:template>
  <xsl:template match="bOff">\texttt{<xsl:apply-templates />}</xsl:template>
  <xsl:template match="bns">\ensuremath{<xsl:apply-templates />}</xsl:template>
  <xsl:template match="mul">\ensuremath{\times{}<xsl:apply-templates />}</xsl:template>
  <xsl:template match="kind">\textscbf{<xsl:apply-templates />}</xsl:template>

  <!-- LIST KINDS -->
  <!-- description-lists -->
  <xsl:template match="description-list">
\begin{description}
    <xsl:for-each select="item">
  \item[<xsl:apply-templates select="description" />] <xsl:apply-templates select="text|numbered-list|description-list|note|example|table|figure|math" />
    </xsl:for-each>
\end{description}
  </xsl:template>
  <!-- itemized lists -->
  <xsl:template match="itemized-list">
\begin{itemize}
      <xsl:for-each select="item">
  \item <xsl:apply-templates select="." />
      </xsl:for-each>
\end{itemize}
  </xsl:template>
  <!-- numbered lists -->
  <xsl:template match="numbered-list" >
\begin{enumerate}
      <xsl:for-each select="item">
  \item <xsl:apply-templates select="." />
      </xsl:for-each>
\end{enumerate}
  </xsl:template>

  <!-- Figure -->
  <!-- figure -->
  <xsl:template match="figure">
\begin{table}[!htb]
  \begin{center}
<xsl:apply-templates select="table" />
<xsl:apply-templates select="caption" />
  \end{center}
\end{table}
  </xsl:template>
  <!-- caption -->
  <xsl:template match="caption">
\caption{<xsl:apply-templates />}
  </xsl:template>
  <!-- tables -->
  <xsl:template match="table">
  \begin{longtable}{|<xsl:for-each select="head/cell"><xsl:choose><xsl:when test="./@colfmt"><xsl:value-of select="./@colfmt" />|</xsl:when><xsl:otherwise>c|</xsl:otherwise></xsl:choose></xsl:for-each>}
  \hline
<xsl:for-each select="head/cell">\textscbf{<xsl:apply-templates />}<xsl:choose><xsl:when test="position()=count(../*)"> \\</xsl:when><xsl:otherwise> &amp;</xsl:otherwise></xsl:choose>
    </xsl:for-each>
  \hline
  \hline
  \endfirsthead
  \hline
<xsl:for-each select="head/cell">\textscbf{<xsl:apply-templates />} \emph{cont'd}<xsl:choose><xsl:when test="position()=count(../*)"> \\</xsl:when><xsl:otherwise> &amp;</xsl:otherwise></xsl:choose>
    </xsl:for-each>
  \hline
  \endhead
      <xsl:for-each select="row">
          <xsl:for-each select="cell">
            <xsl:variable name="cellspan" select="./@span" />
            <xsl:variable name="border" select="./@border" />
            <xsl:variable name="span" select="./@span" />
            <xsl:if test="$span">
\multicolumn{<xsl:value-of select="$span" />}{c}{
            </xsl:if>
  <xsl:apply-templates /><xsl:if test="$span">}</xsl:if><xsl:choose>
              <xsl:when test="position()=count(../*)">\\&#xa;<xsl:if test="not($border='none')">\hline&#xa;</xsl:if>&#xa;</xsl:when>
              <xsl:otherwise>&amp;</xsl:otherwise>
            </xsl:choose>
          </xsl:for-each>
      </xsl:for-each>
  \end{longtable}
  </xsl:template>

  <!-- math -->
  <xsl:template match="equation">&#xa;&#xa;
    \vspace{0in}
    \begin{center}
		<xsl:apply-templates />
    \end{center}
  </xsl:template>
  <xsl:template match="math">\begin{math}<xsl:apply-templates />\end{math}</xsl:template>
  <xsl:template match="mrow"><xsl:apply-templates /></xsl:template>
  <xsl:template match="mi"><xsl:choose><xsl:when test="string-length(text())=1"><xsl:apply-templates /></xsl:when><xsl:otherwise><xsl:apply-templates /></xsl:otherwise></xsl:choose></xsl:template>
  <xsl:template match="mo"><xsl:apply-templates /></xsl:template>
  <xsl:template match="mn"><xsl:apply-templates /></xsl:template>
  <xsl:template match="msup">{<xsl:apply-templates select="./*[position()=1]"/>}^{<xsl:apply-templates select="./*[position()=2]"/>}</xsl:template>
  <xsl:template match="munderover"><xsl:apply-templates select="./*[position()=1]"/>_{<xsl:value-of select="./*[position()=2]"/>}^{<xsl:apply-templates select="./*[position()=3]"/>}</xsl:template>
  <xsl:template match="mfrac">{{<xsl:apply-templates select="./*[position()=1]"/>}\over{<xsl:apply-templates select="./*[position()=2]"/>}}</xsl:template>
  <xsl:template match="mstyle"></xsl:template>


	<!--
		Given a reference to the raw-data section, we build
		a table.
	-->
  <xsl:template match="summarize">
    <!-- A unique hrid to the category we need -->
    <xsl:variable name="hrid" select="./@hrid" />
    <xsl:variable name="scName" select="../title"/>
\begin{longtable}{p{1.25in}<xsl:for-each select="//category[@name=$hrid]/default/field"><xsl:if test="./@title"></xsl:if><xsl:if test="./@table"><xsl:value-of select="./@colfmt" /></xsl:if></xsl:for-each>} 
  <xsl:value-of select="$scName" />
        <xsl:for-each select="//category[@name=$hrid]/default/field">
          <xsl:if test="./@title"></xsl:if>
<xsl:if test="./@table">&amp; \begin{turn}{70}{<xsl:value-of select="@name" />}\end{turn}
          </xsl:if>
        </xsl:for-each>\\
  \hline
  \hline
  \endfirsthead
  <xsl:value-of select="$scName" /> \textit{cont&apos;d}
        <xsl:for-each select="//category[@name=$hrid]/default/field">
          <xsl:if test="./@title">
          </xsl:if>
<xsl:if test="./@table">&amp; \begin{turn}{70}{<xsl:value-of select="@name" />}\end{turn}
          </xsl:if>
        </xsl:for-each> \\
  \hline
  \endhead
<xsl:for-each select="//category[@name=$hrid]/datum">\raggedright <xsl:for-each select="field" >
            <xsl:if test="./@title"><xsl:apply-templates select="." /></xsl:if>
            <xsl:if test="./@table">&amp;<xsl:apply-templates select="." />
            </xsl:if>
          </xsl:for-each>\tabularnewline
      </xsl:for-each>
\end{longtable}
  </xsl:template>

	<!--
		Given a reference to the raw-data section, we build
		a table, then build a descriptor-list.
	-->
  <xsl:template match="reference">
    <!-- A unique hrid to the category we need -->
    <xsl:variable name="hrid" select="./@hrid" />
    <xsl:variable name="scName" select="../title"/>
\begin{longtable}{p{1.25in}<xsl:for-each select="//category[@name=$hrid]/default/field"><xsl:if test="./@title"></xsl:if><xsl:if test="./@table"><xsl:value-of select="./@colfmt" /></xsl:if></xsl:for-each>} 
  <xsl:value-of select="$scName" />
        <xsl:for-each select="//category[@name=$hrid]/default/field">
          <xsl:if test="./@title"></xsl:if>
<xsl:if test="./@table">&amp; \begin{turn}{70}{<xsl:value-of select="@name" />}\end{turn}
          </xsl:if>
        </xsl:for-each>\\
  \hline
  \hline
  \endfirsthead
  <xsl:value-of select="$scName" /> \textit{cont&apos;d}
        <xsl:for-each select="//category[@name=$hrid]/default/field">
          <xsl:if test="./@title">
          </xsl:if>
<xsl:if test="./@table">&amp; \begin{turn}{70}{<xsl:value-of select="@name" />}\end{turn}
          </xsl:if>
        </xsl:for-each> \\
  \hline
  \endhead
<xsl:for-each select="//category[@name=$hrid]/datum">\raggedright <xsl:for-each select="field" >
            <xsl:if test="./@title"><xsl:apply-templates select="." /></xsl:if>
            <xsl:if test="./@table">&amp;<xsl:apply-templates select="." />
            </xsl:if>
          </xsl:for-each>\tabularnewline
      </xsl:for-each>
\end{longtable}
    <!-- Builds the descriptor lists -->
\begin{multicols}{2}
&#xa;
\hspace{-3.75ex}\rulename{Name}
\ruledesc{Description thereof.}\vspace{1ex}
&#xa;
    <xsl:for-each select="//category[@name=$hrid]/datum">
      <xsl:variable name="datum-title" select="field[@title='yes']" />
&#xa;
\hspace{-2ex}\rulename{<xsl:apply-templates select="field[@title='yes']" />}
\ruledesc{<xsl:if test="field[@description='yes']">
  <xsl:for-each select="./field[@description='yes']/child::node()">
    <xsl:choose>
      <xsl:when test="name()='text'">
\parindent=5pt
\everypar{\hangindent=20pt \hangafter=1}<xsl:apply-templates select="child::node()" />
      </xsl:when>
      <xsl:otherwise>
<xsl:apply-templates select="." />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:for-each>
</xsl:if>}\vspace{1ex}
&#xa;
    </xsl:for-each>
\end{multicols}
  </xsl:template>

</xsl:stylesheet>
