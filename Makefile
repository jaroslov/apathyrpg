BUILDSCRIPT = python Tools/build_doc.py

all: standard medieval martialarts

clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *~ *.ilg *.idx

squeaky-clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *.combine.tex *.pdf *.xhtml

standard: standard-wp standard-pdf

medieval: medieval-wp medieval-pdf

martialarts: martialarts-wp martialarts-pdf

character:
	pdflatex CharacterSheet.tex > cs.log
	pdflatex CharacterSheet.tex > cs.log
	pdflatex CharacterSheet.tex > cs.log

standard-wp:
	${BUILDSCRIPT} --prefix=Doc/ -w --retarget-resources  > ARPG.xhtml

medieval-wp:
	${BUILDSCRIPT} --prefix=Doc/ -w --retarget-resources --category-exclusion-list=Tools/Medieval.exl --time-period=1750 > Medieval.xhtml

martialarts-wp:
	${BUILDSCRIPT} --prefix=Doc/ -w --retarget-resources --category-exclusion-list=Tools/MartialArts.exl > MartialArts.xhtml

standard-tex:
	${BUILDSCRIPT} --prefix=Doc/ -l --retarget-resources  > ARPG.tex

medieval-tex:
	${BUILDSCRIPT} --prefix=Doc/ -l --retarget-resources --category-exclusion-list=Tools/Medieval.exl --time-period=1750 > Medieval.tex

martialarts-tex:
	${BUILDSCRIPT} --prefix=Doc/ -l --retarget-resources --category-exclusion-list=Tools/MartialArts.exl > MartialArts.tex

standard-pdf: standard-tex
	pdflatex ARPG.tex > out.log
	makeindex ARPG > out.log
	pdflatex ARPG.tex > out.log
	makeindex ARPG > out.log
	pdflatex ARPG.tex > out.log

medieval-pdf: medieval-tex
	pdflatex Medieval.tex > out.log
	makeindex Medieval > out.log
	pdflatex Medieval.tex > out.log
	makeindex Medieval > out.log
	pdflatex Medieval.tex > out.log

martialarts-pdf: martialarts-tex
	pdflatex MartialArts.tex > out.log
	makeindex MartialArts > out.log
	pdflatex MartialArts.tex > out.log
	makeindex MartialArts > out.log
	pdflatex MartialArts.tex > out.log

showpdf: pdf
	open ARPG.pdf