<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="text" encoding="UTF-8" media-type="text/plain" indent="no"/>
  <xsl:template match="/">
\documentclass[twoside]{book}
\usepackage{include}
\usepackage{pslatex}
\usepackage{psfonts}
\usepackage{multicol}
\usepackage{newcent}
\usepackage{ncntrsbk}
\usepackage{rotating}
\usepackage{tabularx}
\usepackage{array}
\usepackage{longtable}
\usepackage{multirow}
\usepackage{graphicx}
\usepackage{multicolumn}
\usepackage[T1]{fontenc}
\usepackage{hyperref}
\usepackage{wrapfig}
\usepackage[text={5.5in,8in},textheight=8in]{geometry}

\begin{document}

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
\addcontentsline{lof}{section}{\arabic{ExampleCounter} \textsc{#1}}
\vbox{
\textscbf{\noindent Example \arabic{ExampleCounter} {\small \textsc{#1}}}
\begin{quotation}
{\small #2}
\end{quotation}
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
\ARPG \\

\hspace*{6em}
\vbox{\vspace{-2em}
\small Allan Moyse \\
Nathan Jones \\
Jacob Smith \\
Noah Smith \\
Chris Cook \\
Josh Kramer}

\vskip 2in
\textsc{Revision \#1.100000 (06 September 2007)}
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
    <xsl:apply-templates select="part" />
  </xsl:template>
  <!-- Part -->
  <xsl:template match="part">
\part{<xsl:apply-templates select="title" />}
    <xsl:apply-templates select="chapter" />
  </xsl:template>
  <!-- Chapter -->
  <xsl:template match="chapter">
\chapter{<xsl:apply-templates select="title" />}
    <xsl:apply-templates select="section" />
  </xsl:template>
  <!-- Section -->
  <xsl:template match="section">
\<xsl:if test="name(../.)='section'">sub</xsl:if><xsl:if test="name(../../.)='section'">sub</xsl:if><xsl:if test="name(../../../.)='section'">sub</xsl:if>section{<xsl:apply-templates select="title" />}
    <xsl:apply-templates select="./*[position()&gt;1]" />
  </xsl:template>


  <!-- INLINE ELEMENTS -->
  <xsl:template match="title">
    <xsl:apply-templates />
  </xsl:template>
  <!-- Generic Text -->
  <xsl:template match="text()">
    <xsl:variable name="text" select="." />
    <xsl:value-of select="$text" />
  </xsl:template>
  <!-- examples -->
  <xsl:template match="example">
\quotexample[<xsl:apply-templates select="title" />]{<xsl:apply-templates select="text" />}
  </xsl:template>
  <!-- Apathy -->
  <xsl:template match="Apathy">\APATHY{}</xsl:template>
  <!-- and -->
  <xsl:template match="and">\&amp;</xsl:template>
  <!-- special "dollar" word -->
  <xsl:template match="dollar">\$</xsl:template>

</xsl:stylesheet>