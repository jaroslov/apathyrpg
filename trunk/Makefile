LATEX = pdflatex

xhtml: Doc/Apathy.xhtml
	Tools/Builder.py --prefix=Doc/ -wc --retarget-resources

charactersheet: CharacterSheet.tex
	${LATEX} CharacterSheet.tex >& CharacterSheet.pdflatex
