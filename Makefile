MEDIEVAL = --exclude-file=Tools/Medieval.exl

all: pdf webpage

webpage:
	./Tools/Builder.py --prefix=Doc/ -wc --retarget-resources >& webpage.log

pdf:
	./Tools/Builder.py --prefix=Doc/ -l --retarget-resources >& tex.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log