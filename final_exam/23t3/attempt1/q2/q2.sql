-- COMP3311 23T3 Final Exam
-- Q2: print winners (fastest finishers) of each event

-- replace this line with any helper views --

create or replace view finishers(person,age,event,date,time)
as
select p.name, (e.held_on - p.d_o_b) / 365, e.name, e.held_on, r.at_time
from   People p join Participants pp on p.id=pp.person_id
join   Events e on pp.event_id = e.id
join   Checkpoints c on e.route_id = c.route_id
join   Reaches r on partic_id = pp.id and chkpt_id = c.id
where  c.ordering = (select max(ordering) from Checkpoints where route_id = e.route_id)
;

create or replace view q2(event,date,person,time)
as
-- replace this line with your SQL code --
select event, date, person, time
from   Finishers f
where  f.time = (select min(q.time) from Finishers q where q.event=f.event and q.date=f.date)
order  by date, person
;

