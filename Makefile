LATEX = pdflatex
XSLTP = xsltproc

all: xhtml PDF

format: Apathy.xml Apathy.format.xsl
	xsltproc -o Apathy-R.xml Apathy.format.xsl Apathy.xml

xhtml: Apathy.xml Apathy.html.xsl Apathy.css
	xsltproc -o Apathy.xhtml Apathy.html.xsl Apathy.xml

latex: Apathy.tex
	xsltproc -o Apathy.tex Apathy.latex.xsl Apathy.xml

pdf: Apathy.tex
	pdflatex Apathy.tex >& Apathy.pdflatex

pdf2: Apathy.tex
	pdflatex Apathy.tex >& Apathy.pdflatex
	pdflatex Apathy.tex >& Apathy.pdflatex

pdfclean:
	rm -rf *.aux *.lof *.log *.lot *.out *.pdflatex *.toc

pdflatex: latex pdf pdfclean

PDF: pdflatex pdf2
	rm -rf *.aux *.lof *.log *.lot *.out *.pdflatex *.toc

split: Apathy.xml Apathy.split.xsl
	xsltproc -o Apathy.split.xml Apathy.split.xsl Apathy.xml