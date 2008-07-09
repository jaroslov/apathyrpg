MEDIEVAL = --exclude-file=Tools/Medieval.exl

all: pdf webpage

standard: webpage pdf

medieval: medieval-wb medieval-pdf

webpage:
	./Tools/Builder.py --prefix=Doc/ -wc --retarget-resources >& webpage.log

pdf:
	./Tools/Builder.py --prefix=Doc/ -l --retarget-resources >& tex.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log

medieval-wb:
	./Tools/Builder.py ${MEDIEVAL} --prefix=Doc/ -wc --retarget-resources >& webpage.log

medieval-pdf:
	./Tools/Builder.py ${MEDIEVAL} --prefix=Doc/ -l --retarget-resources >& tex.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log