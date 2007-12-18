# dice-regexp: ([0-9]+)[dD]([0-9]+)([+-])?([0-9]+)?
# replace with: <roll><num>\1</num><face>\2</face><bOff>\3</bOff><bns>\4</bns></roll>

Revolvers = """.22 short	<roll><num>1</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	5	8	1	75	4	<dollar/>100.00	<dollar/>0.10
.22 LR	<roll><num>2</num><face>4</face><bOff></bOff><bns></bns></roll>	6	8	1	100	5	<dollar/>125.00	<dollar/>0.10
.32	<roll><num>1</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	6	6 or 8	1	100	6	<dollar/>150.00	<dollar/>0.15
.38 special	<roll><num>1</num><face>8</face><bOff>+</bOff><bns>2</bns></roll>	9	6	1	150	8	<dollar/>200.00	<dollar/>0.25
.357 magnum	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	12	6	1	200	10	<dollar/>300.00	<dollar/>0.50
.44 magnum	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	15	6	1	200	11	<dollar/>350.00	<dollar/>0.75
.45 Long Colt	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	10	6	1	150	10	<dollar/>400.00	<dollar/>0.75
.454 Casull	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	5 or 6	1	200	12	<dollar/>800.00	<dollar/>1.00
.455 Supermag	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	20	6	1	150	13	<dollar/>1,000.00	<dollar/>2.00
.500 S<and/>W	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	20	6	1	150	13	<dollar/>1,000.00	<dollar/>2.00
.500 Linebaugh	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	21	5 or 6	1	200	14	<dollar/>1,200.00	<dollar/>2.00
.500 Linebaugh Long	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	24	5	1	200	15	<dollar/>2,000.00	<dollar/>4.00"""

Pistols = """.25 Auto	<roll><num>2</num><face>4</face><bOff></bOff><bns></bns></roll>	6	14 rnd magazine	1	100	5	<dollar/>100.00	<dollar/>0.20
.32 Auto	<roll><num>1</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	6	14 rnd magazine	1	100	7	<dollar/>150.00	<dollar/>0.25
9mm Parabellum	<roll><num>2</num><face>6</face><bOff></bOff><bns></bns></roll>	8	10 rnd magazine	1	200	9	<dollar/>250.00	<dollar/>0.25
.40 S<and/>W	<roll><num>1</num><face>4</face><bOff>+</bOff><bns>3</bns></roll>	9	10 rnd magazine	1	200	10	<dollar/>250.00	<dollar/>0.50
.357 Sig	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	10	10 rnd magazine	1	250	10	<dollar/>500.00	<dollar/>0.75
10mm Auto	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	12	9 rnd magazine	1	150	11	<dollar/>400.00	<dollar/>0.75
.45 Auto	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	12	8 rnd magazine	1	75	10	<dollar/>300.00	<dollar/>0.50
.440 Cor-Bon	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	15	7 rnd magazine	1	300	11	<dollar/>1,200.00	<dollar/>2.00
.50 Action Express	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	7 rnd magazine	1	200	12	<dollar/>900.00	<dollar/>1.00"""

HuntingPistols = """.223 Remington (5.56mm NATO)	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	14	1 (single shot bolt)	1	500	10	<dollar/>600.00	<dollar/>0.25
.243 Winchester	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>3</bns></roll>	18	1 (single shot bolt)	1	500	11	<dollar/>700.00	<dollar/>0.50
.308 Winchester (7.62 mm NATO)	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	1 (single shot bolt)	1	500	<dollar/>12.00	<dollar/>800.00	<dollar/>0.50
.30-06 Springfield	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	24	1 (single shot bolt)	1	500	13	<dollar/>1,000.00	<dollar/>1.00"""

HeavyPistols = """.224 BOZ	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	14	9 rnd magazine	1	200	11	<dollar/>800.00	<dollar/>3.00
.30 Willis Magnum	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	10 rnd magazine	1	400	13	<dollar/>1,000.00	<dollar/>5.00
.357 Supermagnum	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	21	6 (revolver)	1	300	<dollar/>12.00	<dollar/>750.00	<dollar/>3.00
10 x 64 Borgmaster	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	24	8 rnd magazine	1	100	14	<dollar/>2,000.00	<dollar/>4.00
12 x 70 Borg King	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	30	6 (revolver)	1	100	15	<dollar/>4,000.00	<dollar/>5.00
.500 Sledgehammer	<roll><num>6</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	36	6 rnd magazine	1	100	16	<dollar/>7,200.00	<dollar/>3.00
.525 Sweitzer Ultramax	<roll><num>4</num><face>6</face><bOff>+</bOff><bns>4</bns></roll>	44	5 rnd magazine	1	50	18	<dollar/>10,000.00	<dollar/>8.00
.600 NE-MOD Earth Shaker	<roll><num>6</num><face>4</face><bOff>+</bOff><bns>3</bns></roll>	54	3 (revolver)	1	75	20	<dollar/>25,000.00	<dollar/>100.00"""

GyroRocket = """10mm  Light Gyropistol	<roll><num>2</num><face>6</face><bOff></bOff><bns></bns></roll>	8	8	4	100	8	<dollar/>750.00	<dollar/>5.00
13mm Gyropistol	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	12	6	1	100	8	<dollar/>1,100.00	<dollar/>7.00
16mm  Heavy Gyropistol (FIRE eff.)	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>1</bns></roll> F	15	5	1	100	9	<dollar/>2,000.00	<dollar/>12.00"""

Parts = ["Name","Damage","Average","Capacity","Burst","Range","Minimum Strength","Cost","Ammo"]

def buildOutput(Name,data):
  lines = data.split("\n")
  for line in lines:
    parts = line.split("\t")
    result = "<datum name=\""+parts[0]+"\">\n"
    for fielddata in zip(Parts,parts):
      if fielddata[0] == "Name":
        result += "  <field name=\""+fielddata[0]+"\" title=\"yes\">"+fielddata[1]+"</field>\n"
      elif fielddata[0] == "Average":
        result += "  <field name=\"Kind\" table=\"yes\">"+Name+"</field>\n"
      else:
        result += "  <field name=\""+fielddata[0]+"\" table=\"yes\">"+fielddata[1]+"</field>\n"
    result += "  <field name=\"Description\" description=\"yes\" />\n"
    result += "</datum>"
    print result

buildOutput("Revolvers",Revolvers)
buildOutput("Pistols",Pistols)
buildOutput("Hunting Pistols",HuntingPistols)
buildOutput("Heavy Pistols",HeavyPistols)
buildOutput("Gyro and Rocket Pistols",GyroRocket)