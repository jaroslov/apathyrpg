LATEX = pdflatex
XSLTP = xsltproc

all: xhtml latex pdf

xhtml: Apathy.xml Apathy.html.xsl Apathy.css
	xsltproc -o Apathy.xhtml Apathy.html.xsl Apathy.xml

latex: Apathy.tex
	xsltproc -o Apathy.tex Apathy.latex.xsl Apathy.xml

pdf: Apathy.tex
	pdflatex Apathy.tex

pdflatex: latex pdf