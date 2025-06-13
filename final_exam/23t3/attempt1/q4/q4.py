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
if len(sys.argv) != 2:
    print(USAGE, file=sys.stderr)
    sys.exit(1)
person = sys.argv[1]

try:
    db = psycopg2.connect("dbname=funrun")
    cur = db.cursor()
    q = "select * from finishers where person = %s order by date"
    cur.execute(q,[person])
    runs = cur.fetchall()
    if not runs:
        print("No such person")
    elif len(runs) == 1:
        t1 = runs[0][4]
        print(f"t1={t1}")
        print("Cannot determine a trend")
    elif len(runs) == 2:
        t1 = runs[0][4]
        t2 = runs[1][4]
        print(f"t1={t1}, t2={t2}")
        if t1 > t2:
            print("Improving")
        else:
            print("Not improving")
    elif len(runs) == 3:
        t1 = runs[0][4]
        t2 = runs[1][4]
        t3 = runs[2][4]
        print(f"t1={t1}, t2={t2}, t3={t3}")
        if t1 > t2 and t2 > t3:
            print("Improving")
        else:
            print("Not improving")
    else:
        print("Huh?",len(runs))
    

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
