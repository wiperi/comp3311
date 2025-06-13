-- COMP3311 23T3 Final Exam
-- Q4: Helper SQL views and PlpgSQL functions to support q4.py

create or replace view finishers(person,age,event,date,time)
as
select p.name, (e.held_on - p.d_o_b) / 365, e.name, e.held_on, r.at_time
from   People p join Participants pp on p.id=pp.person_id
join   Events e on pp.event_id = e.id
join   Checkpoints c on e.route_id = c.route_id
join   Reaches r on partic_id = pp.id and chkpt_id = c.id
where  c.ordering = (select max(ordering) from Checkpoints where route_id = e.route_id)
;

