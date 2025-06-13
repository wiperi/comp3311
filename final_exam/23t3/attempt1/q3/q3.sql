-- COMP3311 23T3 Final Exam
-- Q4: Show the "quitters" in an event, and where they gave up

-- replace this line with any helper views or functions --

create or replace view trail(person,event,location,ordering)
as
select p.name, e.id, c.location, c.ordering
from   People p
       join Participants pp on pp.person_id = p.id
       join Events e on pp.event_id = e.id
       join Reaches r on r.partic_id = pp.id
       join CheckPoints c on r.chkpt_id = c.id
order  by p.name,e.id,c.ordering
;

create or replace view farthest(person,event,location,ordering)
as
select person, event, location, ordering
from   trail t
where  ordering = (select max(ordering) from trail where person=t.person and event=t.event)
;

create or replace function q3(_eventID integer)
	returns setof text
as $$
-- replace this line with the body of your PLpgSQL function --
declare
	_last integer;
	_tuple record;
	_nquitters integer := 0;
begin
	perform id from Events where id = _eventID;
	if not found then
		return next 'No such event';
		return;
	end if;
	select max(c.ordering) into _last
	from   CheckPoints c
	       join Events e on c.route_id = e.route_id
	where  e.id = _eventID;
	for _tuple in
		select person,location
		from   farthest
		where  event = _eventID and ordering < _last
	loop
		return next _tuple.person||' gave up at '||_tuple.location;
		_nquitters := _nquitters + 1;
	end loop;
	if _nquitters = 0 then
		return next 'Nobody gave up';
	end if;
end;
$$ language plpgsql;
