clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc

squeaky-clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *.combine.tex *.pdf *.xhtml

test:
	./Tools/build_doc.py --prefix=Doc/ -l --retarget-resources  > tmp.xhtml