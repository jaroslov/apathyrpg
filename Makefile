BUILDSCRIPT = python Tools/build_doc.py

clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc

squeaky-clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *.combine.tex *.pdf *.xhtml

webpage:
	${BUILDSCRIPT} --prefix=Doc/ -w --retarget-resources  > tmp.xhtml

tex:
	${BUILDSCRIPT} --prefix=Doc/ -l --retarget-resources  > tmp.tex

pdf: tex
	pdflatex tmp.tex > out.log
	pdflatex tmp.tex > out.log
	pdflatex tmp.tex > out.log
	pdflatex tmp.tex > out.log

showpdf: pdf
	open tmp.pdf