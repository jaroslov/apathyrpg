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

SubmachinePistols = """9mm Parabellum	<roll><num>2</num><face>6</face></roll>	8	20 rnd magazine	1/6	100	9	<dollar/>1,500.00	<dollar/>0.25
.45 Auto	2D6+1	12	15 rnd magazine	1/4	75	10	<dollar/>2,000.00	<dollar/>0.50"""

SubmachineGuns = """9mm Parabellum	<roll><num>2</num><face>6</face></roll>	8	24 round magazine	4..8	100	9	<dollar/>600.00	<dollar/>0.25
.45 Auto	2D6+1	12	24 round magazine	6	100	10	<dollar/>600.00	<dollar/>0.50"""

ArchaicHandArms = """Muzzleloading pistol	<roll><num>2</num><face>6</face></roll>	8	1	1	75	10	<dollar/>700.00	<dollar/>2.00
Pepperbox	<roll><num>1</num><face>8</face><bOff>+</bOff><bns>2</bns></roll>	9	4..6	1	50	9	<dollar/>1,300.00	<dollar/>2.00
Duck's Foot pistol	<roll><num>2</num><face>6</face></roll>	8	3, all fire at once (3 rnd burst)		30	12	<dollar/>1,000.00	<dollar/>2.00"""

SportingRifles = """.22 LR	<roll><num>2</num><face>4</face><bOff></bOff><bns></bns></roll>	6	10 internal mag	1	100	5	<dollar/>150.00	<dollar/>0.10
.223 Remington (5.56mm NATO)	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	14	5 internal mag	1	600	7	<dollar/>300.00	<dollar/>0.25
.243 Winchester	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>3</bns></roll>	18	5 internal mag	1	600	8	<dollar/>320.00	<dollar/>0.50
6.5 mm x 55 mm Mauser	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>4</bns></roll>	20	4 internal mag	1	500	8	<dollar/>400.00	<dollar/>0.50
7.62 x 39 mm Russian	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>3</bns></roll>	18	5 detatchable mag	1	600	9	<dollar/>300.00	<dollar/>0.20
7 mm Remington Magnum	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	21	4 internal mag	1	700	9	<dollar/>450.00	<dollar/>0.75
.30-30	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	14	6 internal mag	1	200	9	<dollar/>300.00	<dollar/>0.75
.308 Winchester (7.62 mm NATO)	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	4 internal mag	1	800	9	<dollar/>400.00	<dollar/>0.50
.30-06 Springfield	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	24	3 internal mag	1	800	10	<dollar/>400.00	<dollar/>1.00
.300 Winchester Magnum	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	30	3 internal mag	1	1000	11	<dollar/>500.00	<dollar/>1.00
.300 Ultra Magnum	<roll><num>4</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	32	3 internal mag	1	1000	12	<dollar/>670.00	<dollar/>1.40
.375 H<and/>H Magnum	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>4</bns></roll>	33	3 internal mag	1	800	13	<dollar/>800.00	<dollar/>2.00
45-70 Gov't	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	5 internal mag	1	100	10	<dollar/>350.00	<dollar/>1.00"""

BigGameRifles = """.416 Rigby	<roll><num>6</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	36	3 internal mag	1	500	12	<dollar/>2,000.00	<dollar/>5.00
.416 Remington	<roll><num>6</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	36	3 internal mag	1	500	12	<dollar/>2,000.00	<dollar/>5.00
.458 Winchester Magnum	<roll><num>5</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	40	3 internal mag	1	400	13	<dollar/>2,500.00	<dollar/>8.00
.500 Nitro Express	<roll><num>4</num><face>6</face><bOff>+</bOff><bns>4</bns></roll>	44	2 breech load (double)	2	300	13	<dollar/>8,000.00	<dollar/>12.00
.500 Jeffery	<roll><num>4</num><face>6</face><bOff>+</bOff><bns>4</bns></roll>	44	2 breech load (double)	2	300	13	<dollar/>8,000.00	<dollar/>12.00
.577 A-square Tyrannosaur	<roll><num>6</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	48	3 internal mag	1	300	15	<dollar/>5,500.00	<dollar/>20.00
.600 Nitro Express	<roll><num>6</num><face>4</face><bOff>+</bOff><bns>3</bns></roll>	54	2 breech load (double)	2	300	14	<dollar/>12,000.00	<dollar/>20.00
.700 Nitro Express	<roll><num>6</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	60	2 breech load (double)	2	300	15	<dollar/>25,000.00	<dollar/>50.00"""

AntiArmorRifles = """.375-.50 Mach 5	4x<roll><num>2</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	40	1 (single shot bolt)	1	2500	10	<dollar/>4,500.00	<dollar/>10.00
.50 BMG	3x <roll><num>3</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	45	5 round magazine	1	1500	10	<dollar/>2,200.00	<dollar/>4.00
12.7 - 14.5 mm Gungnir	<roll><num>5</num><face>4</face><bOff>+</bOff><bns>6</bns></roll>	60	1 (single shot bolt)	1	3500	11	<dollar/>19,000.00	<dollar/>20.00
15 mm Std. Light Anti-Armor Gun	2x <roll><num>3</num><face>4</face><bOff>+</bOff><bns>6</bns></roll>	72	3 round magazine	1	800	12	<dollar/>9,000.00	<dollar/>20.00
15 mm S<and/>H Longboy	<roll><num>6</num><face>6</face><bOff>+</bOff><bns>6</bns></roll>	84	1 (single shot bolt)	1	1000	13	<dollar/>13,000.00	<dollar/>25.00
20 mm Lahti	3x <roll><num>5</num><face>6</face><bOff></bOff><bns></bns></roll>	90	3 round magazine	1	1000	13	<dollar/>22,000.00	<dollar/>30.00
20 mm Getemono Mini-14 *	5x <roll><num>2</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	70	8 round magazine	1	300	11	<dollar/>20,000.00	<dollar/>100.00
25 mm S<and/>H Thunderbolt	4x <roll><num>5</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	100	1 (single shot bolt)	1	1200	14	<dollar/>34,000.00	<dollar/>40.00
25 mm S<and/>H Azazel	3x <roll><num>4</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	120	5 round cylinder	1	3000	16	<dollar/>56,000.00	<dollar/>500.00
25 mm S<and/>H Fist of God	6x <roll><num>5</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	150	1 (single shot bolt)	1	1000	18	<dollar/>48,000.00	<dollar/>80.00
30 mm S<and/>H Gottdamerung	8x <roll><num>5</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	200	1 (single shot bolt)	1	1000	20	<dollar/>95,000.00	<dollar/>1,000.00"""

SniperRifles = """.223 Remington (5.56mm NATO)	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	14	5 round magazine	1	1000	7	<dollar/>800.00	<dollar/>1.00
.308 Winchester (7.62 mm NATO)	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	5 round magazine	1	1000	9	<dollar/>900.00	<dollar/>1.00
.300 Winchester Magnum	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	30	5 round magazine	1	1000	11	<dollar/>1,000.00	<dollar/>1.50
.338 Lapua Magnum	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>5</bns></roll>	33	5 round magazine	1	1200	12	<dollar/>1,300.00	<dollar/>3.00
.409 Cheyenne Tactical	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>5</bns></roll>	33	5 round magazine	1	1500	12	<dollar/>2,400.00	<dollar/>5.00
.50 BMG	3x <roll><num>3</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	45	5 round magazine	1	1500	12	<dollar/>5,000.00	<dollar/>8.00"""

SportingShotguns = """.410 Bore	<roll><num>2</num><face>6</face><bOff></bOff><bns></bns></roll>	8			30	8	<dollar/>170.00	<dollar/>0.50
20 gauge or 28 gauge	<roll><num>3</num><face>6</face><bOff></bOff><bns></bns></roll>	12			40	10	<dollar/>240.00	<dollar/>1.00
16 gauge (rare)	<roll><num>4</num><face>6</face><bOff></bOff><bns></bns></roll>	16			50	11	<dollar/>300.00	<dollar/>1.00
12 gauge	<roll><num>5</num><face>6</face><bOff></bOff><bns></bns></roll>	20			50	12	<dollar/>350.00	<dollar/>1.25
10 gauge (modern)	<roll><num>5</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	30			80	12	<dollar/>800.00	<dollar/>3.00
8 gauge (archaic)	<roll><num>4</num><face>4</face><bOff>+</bOff><bns>4</bns></roll>	40	2 breech load (double)	2	100	14	<dollar/>3,000.00	<dollar/>5.00
4 gauge (archaic)	<roll><num>5</num><face>4</face><bOff>+</bOff><bns>5</bns></roll>	55	2 breech load (double)	2	100	16	<dollar/>10,000.00	<dollar/>10.00"""

MilitaryShotguns = """20 gauge auto	<roll><num>3</num><face>6</face><bOff></bOff><bns></bns></roll>	12	20	4	40	11	<dollar/>800.00	<dollar/>1.00
20 gauge Lawmaker belt-fed	<roll><num>3</num><face>6</face><bOff></bOff><bns></bns></roll>	12	100	6	40	12	<dollar/>15,000.00	<dollar/>1.00
12 gauge auto	<roll><num>5</num><face>6</face><bOff></bOff><bns></bns></roll>	20	12	4	50	13	<dollar/>1,000.00	<dollar/>1.25
12 Gauge Executioner belt-fed	<roll><num>5</num><face>6</face><bOff></bOff><bns></bns></roll>	20	64	6	50	15	<dollar/>22,000.00	<dollar/>1.25
10 gauge auto	<roll><num>5</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	30	12	4	80	14	<dollar/>8,000.00	<dollar/>3.00
10 gauge BorgBuster belt-fed	<roll><num>5</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	30	64	6	80	16	<dollar/>40,000.00	<dollar/>3.00"""

AssaultRifles = """.223 Remington (5.56mm NATO)	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	14	30 round magazine	8	300	9	<dollar/>700.00	<dollar/>0.25
7.62 x 39 mm Russian	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>3</bns></roll>	18	30 round magazine	6	300	10	<dollar/>500.00	<dollar/>0.20
.30 BMG	<roll><num>4</num><face>6</face><bOff></bOff><bns></bns></roll>	16	50 round mini-drum	6	300	10	<dollar/>700.00	<dollar/>1.00
.308 Winchester (7.62 mm NATO)	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	30 round magazine	6	300	11	<dollar/>700.00	<dollar/>0.50"""

ArchaicLongArms = """blunderbuss (light)	<roll><num>2</num><face>6</face><bOff></bOff><bns></bns></roll>	8	1 (muzzle loaded)	1	30	8	<dollar/>500.00	<dollar/>3.00
blunderbuss (medium)	<roll><num>3</num><face>6</face><bOff></bOff><bns></bns></roll>	12	1 (muzzle loaded)	1	50	10	<dollar/>750.00	<dollar/>4.00
blunderbuss (heavy)	<roll><num>4</num><face>6</face><bOff></bOff><bns></bns></roll>	16	1 (muzzle loaded)	1	75	11	<dollar/>1,000.00	<dollar/>5.00
Light Musket	<roll><num>3</num><face>6</face><bOff></bOff><bns></bns></roll>	12	1 (muzzle loaded)	1	150	9	<dollar/>1,000.00	<dollar/>4.00
Medium Musket	<roll><num>4</num><face>6</face><bOff></bOff><bns></bns></roll>	16	1 (muzzle loaded)	1	200	10	<dollar/>1,500.00	<dollar/>5.00
Heavy Musket	<roll><num>5</num><face>6</face><bOff></bOff><bns></bns></roll>	20	1 (muzzle loaded)	1	300	12	<dollar/>2,000.00	<dollar/>6.00
Swivel Gun / deck gun	<roll><num>6</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	36	1 (muzzle loaded)	1	100	fixed	<dollar/>5,000.00	<dollar/>5.00
black powder rifle (light)	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	1 (muzzle loaded)	1	300	9	<dollar/>1,500.00	<dollar/>4.00
black powder rifle (medium)	<roll><num>4</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	24	1 (muzzle loaded)	1	400	10	<dollar/>2,000.00	<dollar/>5.00
black powder rifle (heavy)	<roll><num>5</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	30	1 (muzzle loaded)	1	500	12	<dollar/>2,800.00	<dollar/>6.00"""

Parts = ["Name","Damage","Average","Capacity","Burst","Range","Minimum Strength","Cost","Ammo"]

def buildOutput(Name,data):
  lines = data.split("\n")
  for line in lines:
    parts = line.split("\t")
    result = "<datum name=\""+parts[0]+"\">\n"
    for fielddata in zip(Parts,parts):
      if fielddata[0] == "Name":
        result += "  <field name=\""+fielddata[0]+"\" title=\"yes\">"+fielddata[1]+"</field>\n"
        result += "  <field name=\"Kind\" table=\"yes\">"+Name+"</field>\n"
      elif fielddata[0] == "Average":
        pass
      else:
        result += "  <field name=\""+fielddata[0]+"\" table=\"yes\">"+fielddata[1]+"</field>\n"
    result += "  <field name=\"Description\" description=\"yes\" />\n"
    result += "</datum>"
    print result

#buildOutput("Revolver",Revolvers)
#buildOutput("Pistol",Pistols)
#buildOutput("Hunting Pistol",HuntingPistols)
#buildOutput("Heavy Pistol",HeavyPistols)
#buildOutput("Gyro-rocket Pistol",GyroRocket)
#buildOutput("Submachine Pistol",SubmachinePistols)
#buildOutput("Archaic Hand-arm",ArchaicHandArms)

buildOutput("Sporting Rifles",SportingRifles)
buildOutput("Big Game Rifle",BigGameRifles)
buildOutput("Anti-armor Rifle",AntiArmorRifles)
buildOutput("Sniper Rifle",SniperRifles)
buildOutput("Sporting Shotgun",SportingShotguns)
buildOutput("Military Shotgun",MilitaryShotguns)
buildOutput("Assault Rifle",AssaultRifles)
buildOutput("Archaic Long-arm",ArchaicLongArms)