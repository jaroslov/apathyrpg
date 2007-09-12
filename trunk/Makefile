LATEX = pdflatex
XSLTP = xsltproc

all: xhtml latex pdf

format: Apathy.xml Apathy.format.xsl
	xsltproc -o Apathy-R.xml Apathy.format.xsl Apathy.xml

xhtml: Apathy.xml Apathy.html.xsl Apathy.css
	xsltproc -o Apathy.xhtml Apathy.html.xsl Apathy.xml

latex: Apathy.tex
	xsltproc -o Apathy.tex Apathy.latex.xsl Apathy.xml

pdf: Apathy.tex
	pdflatex Apathy.tex

pdflatex: latex pdf