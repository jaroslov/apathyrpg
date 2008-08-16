BUILDSCRIPT = python Tools/build_doc.py
APATHYPREFIX = Doc/Apathy
BOMPREFIX = Doc/BookOfMagic

all: standard medieval martialarts

all-pdf: medieval-pdf standard-pdf

clean:
	rm -f *.aux *.lof *.log *.lot *.out *.toc *~ *.ilg *.idx *ind

squeaky-clean: clean
	rm -f *.aux *.lof *.log *.lot *.out *.toc *.combine.tex *.pdf *.xhtml *.tex

standard: standard-wp standard-pdf

medieval: medieval-wp medieval-pdf

martialarts: martialarts-wp martialarts-pdf

character:
	pdflatex Doc/CharacterSheet.tex > cs.log
	pdflatex Doc/CharacterSheet.tex > cs.log
	pdflatex Doc/CharacterSheet.tex > cs.log

standard-wp:
	${BUILDSCRIPT} --prefix=${APATHYPREFIX} -w --retarget-resources  > ARPG.xhtml

medieval-wp:
	${BUILDSCRIPT} --prefix=${APATHYPREFIX} -w --retarget-resources --category-exclusion-list=Tools/Medieval.exl --time-period=1750 > Medieval.xhtml

martialarts-wp:
	${BUILDSCRIPT} --prefix=${APATHYPREFIX} -w --retarget-resources --category-exclusion-list=Tools/MartialArts.exl > MartialArts.xhtml

bookofmagic-wp:
	${BUILDSCRIPT} --prefix=${BOMPREFIX} --main-document=main --retarget-resources -w > BOM.xhtml

standard-tex:
	${BUILDSCRIPT} --prefix=${APATHYPREFIX} -l --retarget-resources  > ARPG.tex

medieval-tex:
	${BUILDSCRIPT} --prefix=${APATHYPREFIX} -l --retarget-resources --category-exclusion-list=Tools/Medieval.exl --time-period=1750 > Medieval.tex

martialarts-tex:
	${BUILDSCRIPT} --prefix=${APATHYPREFIX} -l --retarget-resources --category-exclusion-list=Tools/MartialArts.exl > MartialArts.tex

bookofmagic-tex:
	${BUILDSCRIPT} --prefix=${BOMPREFIX} --main-document=main --retarget-resources -l > BOM.tex

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

bookofmagic-pdf: bookofmagic-tex
	pdflatex BOM.tex > out.log
	makeindex BOM > out.log
	pdflatex BOM.tex > out.log
	makeindex BOM > out.log
	pdflatex BOM.tex > out.log

showpdf: pdf
	open ARPG.pdf