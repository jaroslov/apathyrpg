MEDIEVAL = --exclude-file=Tools/Medieval.exl

all: pdf webpage medieval-wb medieval-pdf

clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc

squeaky-clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *.combine.tex *.pdf *.xhtml

test:
	./Tools/build_doc.py --prefix=Doc/ -w --retarget-resources > tmp.xhtml

webpage:
	./Tools/Builder.py --prefix=Doc/ -wc --retarget-resources >& webpage.log
	mv Apathy.combine.xhtml Apathy.xhtml

pdf:
	./Tools/Builder.py --prefix=Doc/ -l --retarget-resources >& tex.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	pdflatex Apathy.combine.tex >& pdf.log
	mv Apathy.combine.pdf Apathy.p