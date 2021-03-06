from random import choice

names = ["Caspar", "Oliver", "Jack", "Harry", "Jacob", "Charlie", "Thomas", "George", "Oscar", "James", "William", "Noah", "Alfie", "Joshua", "Muhammad", "Amelia", "Olivia", "Isla", "Emily", "Poppy", "Ava", "Isabella", "Jessica", "Lily", "Sophie", "Grace", "Sophia", "Mia", "Evie"]
seats = ['A01','A02','A03','A04','A05','A06','A07','A08','A09','A10','A11','A12','A13','A14','A15','A16','A17','A18','A19','A20','B01','B02','B03','B04','B05','B06','B07','B08','B09','B10','B11','B12','B13','B14','B15','B16','B17','B18','B19','B20','C01','C02','C03','C04','C05','C06','C07','C08','C09','C10','C11','C12','C13','C14','C15','C16','C17','C18','C19','C20','D01','D02','D03','D04','D05','D06','D07','D08','D09','D10','D11','D12','D13','D14','D15','D16','D17','D18','D19','D20','E01','E02','E03','E04','E05','E06','E07','E08','E09','E10','E11','E12','E13','E14','E15','E16','E17','E18','E19','E20','F01','F02','F03','F04','F05','F06','F07','F08','F09','F10','F11','F12','F13','F14','F15','F16','F17','F18','F19','F20','G01','G02','G03','G04','G05','G06','G07','G08','G09','G10','G11','G12','G13','G14','G15','G16','G17','G18','G19','G20','H01','H02','H03','H04','H05','H06','H07','H08','H09','H10','H11','H12','H13','H14','H15','H16','H17','H18','H19','H20','I01','I02','I03','I04','I05','I06','I07','I08','I09','I10','I11','I12','I13','I14','I15','I16','I17','I18','I19','I20','J01','J02','J03','J04','J05','J06','J07','J08','J09','J10','J11','J12','J13','J14','J15','J16','J17','J18','J19','J20','K01','K02','K03','K04','K05','K06','K07','K08','K09','K10','K11','K12','K13','K14','K15','K16','K17','K18','K19','K20','L01','L02','L03','L04','L05','L06','L07','L08','L09','L10','L11','L12','L13','L14','L15','L16','L17','L18','L19','L20','M01','M02','M03','M04','M05','M06','M07','M08','M09','M10','M11','M12','M13','M14','M15','M16','M17','M18','M19','M20','N01','N02','N03','N04','N05','N06','N07','N08','N09','N10','N11','N12','N13','N14','N15','N16','N17','N18','N19','N20','O01','O02','O03','O04','O05','O06','O07','O08','O09','O10','O11','O12','O13','O14','O15','O16','O17','O18','O19','O20','P01','P02','P03','P04','P05','P06','P07','P08','P09','P10','P11','P12','P13','P14','P15','P16','P17','P18','P19','P20','Q01','Q02','Q03','Q04','Q05','Q06','Q07','Q08','Q09','Q10','Q11','Q12','Q13','Q14','Q15','Q16','Q17','Q18','Q19','Q20','R01','R02','R03','R04','R05','R06','R07','R08','R09','R10','R11','R12','R13','R14','R15','R16','R17','R18','R19','R20','S01','S02','S03','S04','S05','S06','S07','S08','S09','S10','S11','S12','S13','S14','S15','S16','S17','S18','S19','S20','T01','T02','T03','T04','T05','T06','T07','T08','T09','T10','T11','T12','T13','T14','T15','T16','T17','T18','T19','T20','U01','U02','U03','U04','U05','U06','U07','U08','U09','U10','U11','U12','U13','U14','U15','U16','U17','U18','U19','U20','V01','V02','V03','V04','V05','V06','V07','V08','V09','V10','V11','V12','V13','V14','V15','V16','V17','V18','V19','V20','W01','W02','W03','W04','W05','W06','W07','W08','W09','W10','W11','W12','W13','W14','W15','W16','W17','W18','W19','W20','X01','X02','X03','X04','X05','X06','X07','X08','X09','X10','X11','X12','X13','X14','X15','X16','X17','X18','X19','X20','Y01','Y02','Y03','Y04','Y05','Y06','Y07','Y08','Y09','Y10','Y11','Y12','Y13','Y14','Y15','Y16','Y17','Y18','Y19','Y20','Z01','Z02','Z03','Z04','Z05','Z06','Z07','Z08','Z09','Z10','Z11','Z12','Z13','Z14','Z15','Z16','Z17','Z18','Z19','Z20']
performance_ids = []

num_performances = 50
for i in range(1,num_performances+1):
	for _ in range(num_performances+1-i):
		performance_ids.append(i)

booked = []

out = open("bookings.sql", 'w')

total_seats_avaiable = num_performances * len(seats)

while len(booked)<(total_seats_avaiable/2):
	seat = choice(seats)
	pid = choice(performance_ids)
	booking = (seat, pid)
	if booking not in booked:
	    booked.append(booking)


booked = sorted(booked, key = lambda (seat, pid) : seat)
booked = sorted(booked, key = lambda (seat, pid) : pid)

for seat, pid in booked:
	name = choice(names)
	out.write("insert into Booking values (NULL, \"%s\", %2d, \"%s\", \"%s@example.com\");\n" % (seat, pid, name, name))

out.close