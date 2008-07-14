clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc

squeaky-clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *.combine.tex *.pdf *.xhtml

webpage:
	./Tools/build_doc.py --prefix=Doc/ -w --retarget-resources  > tmp.xhtml

pdf:
	./Tools/build_doc.py --prefix=Doc/ -l --retarget-resources  > tmp.tex
	pdflatex tmp.tex > out.log
	pdflatex tmp.tex > out.log
	pdflatex tmp.tex > out.log
	pdflatex tmp.tex > out.log