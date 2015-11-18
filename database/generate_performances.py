import random

plays = ['The Wizard of Oz', 'Singin\' in the Rain', 'West Side Story', 'Cabaret', 
'39 Steps', 'Antlia Pneumatica', 'The Winter\'s Tale', 'A Midsummer Night\'s Dream']

start_times = ["19:00:00", "19:15:00", "19:30:00", "19:45:00"]

year = 2016
month = 01
day = 10

def next_date():
    global year
    global month
    global day
    s = "{year}-{month:0>2d}-{day:0>2d}".format(year=year, month=month, day=day)
    day += 1
    if day == 31 or (month==2 and day==28):
        month +=1
        day = 1
    return s

def get_date_time():
    roll = random.randint(0,5)
    if roll!=0:
        return next_date() +" "+ random.choice(start_times)
    else:
        next_date()
        return next_date() +" "+ random.choice(start_times)
    

for i in range(50):
    print "insert into Performance values (\"" + get_date_time() + "\", \'" + random.choice(plays).replace('\'', '\\\'') + "\');"