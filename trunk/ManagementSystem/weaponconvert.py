data = """.22 short	<roll><num>1</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	5	8	1	75	4	$100.00	$0.10
.22 LR	<roll><num>2</num><face>4</face><bOff></bOff><bns></bns></roll>	6	8	1	100	5	$125.00	$0.10
.32	<roll><num>1</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	6	6 or 8	1	100	6	$150.00	$0.15
.38 special	<roll><num>1</num><face>8</face><bOff>+</bOff><bns>2</bns></roll>	9	6	1	150	8	$200.00	$0.25
.357 magnum	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	12	6	1	200	10	$300.00	$0.50
.44 magnum	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	15	6	1	200	11	$350.00	$0.75
.45 Long Colt	<roll><num>2</num><face>4</face><bOff>+</bOff><bns>1</bns></roll>	10	6	1	150	10	$400.00	$0.75
.454 Casull	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>1</bns></roll>	18	5 or 6	1	200	12	$800.00	$1.00
.455 Supermag	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	20	6	1	150	13	$1,000.00	$2.00
.500 S<and/>W	<roll><num>2</num><face>6</face><bOff>+</bOff><bns>3</bns></roll>	20	6	1	150	13	$1,000.00	$2.00
.500 Linebaugh	<roll><num>3</num><face>4</face><bOff>+</bOff><bns>2</bns></roll>	21	5 or 6	1	200	14	$1,200.00	$2.00
.500 Linebaugh Long	<roll><num>3</num><face>6</face><bOff>+</bOff><bns>2</bns></roll>	24	5	1	200	15	$2,000.00	$4.00"""

Parts = ["Name","Damage","Capacity","Burst","Minimum Strength","Cost","Ammo"]

lines = data.split("\n")
for line in lines:
  parts = line.split("\t")
  result = "<datum name=\""+parts[0]+"\">\n"
  for fielddata in zip(Parts,parts):
    if fielddata[0] == "Name":
      result += "  <field name=\""+fielddata[0]+"\" title=\"yes\">"+fielddata[1]+"</field>\n"
    else:
      result += "  <field name=\""+fielddata[0]+"\" table=\"yes\">"+fielddata[1]+"</field>\n"
  result += "  <field name=\"Description\" description=\"yes\" />\n"
  result += "</datum>"
  print result
