MEDIEVAL = --exclude-file=Tools/Medieval.exl

all: pdf webpage medieval-wb medieval-pdf

clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc

standard: webpage pdf

medieval: medieval-wb medieval-pdf

webpage:
	./Tools/Builder.py --prefix=Doc/ -wc --retarget-resources >& webpage.log
	mv Apathy.combine.xhtml Apathy.xhtml

pdf:
	./Tools/Builder.py --prefix=Doc/ -l --retarget-resources >& tex.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	mv Apathy.combine.pdf Apathy.pdf

medieval-wb:
	./Tools/Builder.py ${MEDIEVAL} --prefix=Doc/ -wc --retarget-resources >& webpage.log
	mv Apathy.combine.xhtml Apathy.medieval.xhtml

medieval-pdf:
	./Tools/Builder.py ${MEDIEVAL} --prefix=Doc/ -l --retarget-resources >& tex.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	mv Apathy.combine.pdf Apathy.medieval.pdf