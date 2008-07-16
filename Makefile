BUILDSCRIPT = python Tools/build_doc.py

clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc

squeaky-clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *.combine.tex *.pdf *.xhtml

standard: standard-wp standard-pdf

medieval: medieval-wp medieval-pdf

martialarts: martialarts-wp martialarts-pdf

standard-wp:
	${BUILDSCRIPT} --prefix=Doc/ -w --retarget-resources  > ARPG.xhtml

medieval-wp:
	${BUILDSCRIPT} --prefix=Doc/ -w --retarget-resources --category-exclusion-list=Tools/Medieval.exl --time-period=1750 > Medieval.arpg.xhtml

martialarts-wp:
	${BUILDSCRIPT} --prefix=Doc/ -w --retarget-resources --category-exclusion-list=Tools/MartialArts.exl > MartialArts.arpg.xhtml

standard-tex:
	${BUILDSCRIPT} --prefix=Doc/ -l --retarget-resources  > ARPG.tex

medieval-tex:
	${BUILDSCRIPT} --prefix=Doc/ -l --retarget-resources --category-exclusion-list=Tools/Medieval.exl --time-period=1750 > Medieval.arpg.tex

martialarts-tex:
	${BUILDSCRIPT} --prefix=Doc/ -l --retarget-resources --category-exclusion-list=Tools/MartialArts.exl > MartialArts.arpg.tex

standard-pdf: standard-tex
	pdflatex ARPG.tex > out.log
	pdflatex ARPG.tex > out.log
	pdflatex ARPG.tex > out.log

medieval-pdf: medieval-tex
	pdflatex Medieval.arpg.tex > out.log
	pdflatex Medieval.arpg.tex > out.log
	pdflatex Medieval.arpg.tex > out.log

martialarts-pdf: martialarts-tex
	pdflatex MartialArts.arpg.tex > out.log
	pdflatex MartialArts.arpg.tex > out.log
	pdflatex MartialArts.arpg.tex > out.log


showpdf: pdf
	open ARPG.pdf