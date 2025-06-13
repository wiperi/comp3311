#! /usr/bin/env python3

# COMP3311 23T3 Final Exam
# Q5: Print information about age categories in a given Event

import sys
import psycopg2

### Constants
USAGE = f"Usage: {sys.argv[0]} Event Year"

### Globals
db = None

### Queries

### replace this line with any query templates ###

### Command-line args
if len(sys.argv) != 3:
    print(USAGE, file=sys.stderr)
    sys.exit(1)
event = sys.argv[1]
year = sys.argv[2]
ranges = [("under 20",0,19),("20-24",20,24),("25-29",25,29),("30-34",30,34),("35 and over",35,99)]
q0 = """
select name,held_on from Events
where name ~* %s and held_on::text ~* %s
"""
q1 = """
select * from finishers
where  event ~* %s and date::text ~* %s
       and age between %s and %s
order  by time,person
"""

try:
    ### replace this line with your Python code ###
    db = psycopg2.connect("dbname=funrun")
    cur = db.cursor()
    cur.execute(q0,[event,year])
    events = cur.fetchall()
    if len(events) == 0:
        print("No matching event")
        exit()
    if len(events) > 1:
        print("Event/year is ambiguous")
        exit()
    name,held = events[0]
    print(f"{name}, {held}")
    for r in ranges:
        cur.execute(q1,[event,year,r[1],r[2]])
        print(r[0])
        runners = cur.fetchall()
        if len(runners) == 0:
            print("- no participants in this age group")
            continue
        time = 0
        for t in runners:
            name,age,x,y,mins = t
            if time == 0:
                print(f"- {name}, {age}yo, {mins}mins")
                time = mins
            elif time == mins:
                print(f"- {name}, {age}yo, {mins}mins")
            else:
                break

except psycopg2.Error as err:
    print("DB error: ", err)
except Exception as err:
    print("Internal Error: ", err)
    raise err
finally:
    if db is not None:
        db.close()
sys.exit(0)

### replace this line by any helper functions ###
