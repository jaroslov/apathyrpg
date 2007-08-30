all: ARPGData CoreRules

ARPGData: ../Game/ARPG-Data.xml getxsd.py
	python getxsd.py ../Game/ARPG-Data.xml > ARPG-Data.dot
	dot -Tpng ARPG-Data.dot -o ARPG-Data.png
	open ARPG-Data.png

CoreRules: ../Game/CoreRules.xml getxsd.py
	python getxsd.py ../Game/CoreRules.xml > CoreRules.dot
	dot -Tpng CoreRules.dot -o CoreRules.png
	open CoreRules.png