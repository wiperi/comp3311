<?php
require("../../course.php"); $exID = "02";
$PGDOC = "https://www.postgresql.org/docs/17/";
$exWeb = WEBHOME."/pracs/$exID"; $exDir = HOMEDIR."/pracs/$exID";
echo startPage("Prac Exercise $exID","","Schema definition, data constraints");
echo updateBlurb();
?>

<h3>Aims</h3>

This exercise aims to give you practice in:
<ul>
<li> specifying a schema using the SQL data definition language
<li> defining constraints on data
<li> loading tuples into a database
</ul><br/>
<p>
Note that, unlike previous Prac Exercises, this exercise will <b>not</b>
explain how to do everything. Part of the aim of the exercise is that you
explore how to use the PostgreSQL system. A very important tool for this
is the <a href="<?=$PGDOC?>/index.html">PostgreSQL Manual</a>.
For this exercise, we will give pointers to the relevant manual sections;
after this, we will expect you to go and find out how to do things yourself.
There are 10 tasks in total for this Prac Exercise.
</p>

<h3>Background</h3>

<p>
We wish to build a simple database for a company which
has a number of departments. Each department has a manager and a
<q>mission statement</q>, which is defined by a number of key words
(e.g. <q>commitment</q>, <q>service</q>, <q>innovation</q>, etc.). The
company also uses numeric codes to identify each department. For
each employee, we need to know their name and tax file number (for
payroll purposes), and also the total number of hours that they work
each week. Employees may work in several departments, and the percentage
of their total hours spent in each department needs to be recorded; they
have to work in at least one department. Each department has a manager,
and they work full-time in that role.
</p>
<p>
A possible ER design for this company is as follows:
</p>
<center>
<table border='1' cellpadding='10' cellspacing='0'><tr><td>
<img src='Pic/company1.png'>
</td></tr></table>
</center>
<p>
Use this design as the basis for the rest of the Lab.
</p>
<p>
Consider now some facts about the company:
</p>
<ul>
<li> there are three departments: Administration, Research, Sales
<li> Administration has department# 001
<li> Administration's <q>mission</q> is: innovation, reliability, profit
<li> Research has department# 003
<li> Research's <q>mission</q> is: innovation, technology
<li> Sales has department# 002
<li> Sales' <q>mission</q> is: customer-focus, growth
<li> Administration is managed by John Smith, who works 40 hours per week
<li> Research is managed by Walter Wong, who works 50 hours per week
<li> Sales is managed by Pradeep Sharma, who works 30 hours per week
<li> Tom Robbins works 35 hours/week, half-time in Administration and half-time in Sales
<li> Adam Spencer works 50 hours/week mostly (90%) in Sales, and the rest of the time in Administration
<li> Susan Ryan works in Administration for 60 hours/week
<li> Steven Smooth is a full-time Sales worker (45 hours/week)
<li> Max Schmidt, Maria Orlowska and Yusif Budianto work full-time (40 hours/week) in Research
</ul><br/>
<p>
The following data, obtained from the Australian Tax Office (:-),
gives tax file numbers for each of the employees noted above:
</p>
<center>
<table border='1' cellpadding='3' cellspacing='0'>
<tr><td><b>Employee</b></td><td><b>Tax File #</b></td></tr>
<tr><td>Yusif Budianto</td><td>777-654-321</td></tr>
<tr><td>Maria Orlowska</td><td>123-987-654</td></tr>
<tr><td>Tom Robbins</td><td>323-626-929</td></tr>
<tr><td>Susan Ryan</td><td>993-893-864</td></tr>
<tr><td>Max Schmidt</td><td>419-813-573</td></tr>
<tr><td>Pradeep Sharma</td><td>222-333-444</td></tr>
<tr><td>John Smith</td><td>123-234-456</td></tr>
<tr><td>Steven Smooth</td><td>632-647-973</td></tr>
<tr><td>Adam Spencer</td><td>747-400-123</td></tr>
<tr><td>Walter Wong</td><td>326-888-711</td></tr>
</table>
</center>

<h3>Exercises</h3>

<ol>

<li>
<p><b>Download the files.</b></p>
<p>
There are several files available for this exercise, primarily:
</p>
<ul>
<li>
<p>
	<a href='schema.sql'>schema.sql</a> which contains a relational
	schema for the above ER design except that it is missing all
	of the constraints suggested by the diagram, and is also
	missing a number of common-sense or application constraints
</p>
<li>
<p>
	<a href='data.sql'>data.sql</a> which contains a collection
	of valid tuples to populate this schema, based on the above
	description, and satisfying all of the domain constraints
</p>
<li>
<p>
	<a href='bad.sql'>bad.sql</a> which contains a collection of
	invalid tuples for this schema
</p>
</ul>
<p>
To copy these files into your working directory,
run the following commands in a terminal window:
</p>
<pre>
$ <b>mkdir -p ~/cs3311/prac02</b>  <span class="comment"># or choose another folder that suits (see note below)</span>
$ <b>cp ~cs3311/web/24T1/pracs/02/*.sql ~/cs3311/prac02</b>
$ <b>cd ~/cs3311/prac02</b>
</pre>
<p>
<p>
This will put the above files,
plus a few others to use later in the Prac Exercise,
into your working directory for this Prac.
Once you've copied the files, change into the working directory
(<tt>~/cs3311/prac03</tt> or whatever you called it).
Stay in this directory while you're working on this Prac.
</p>
<p>
Note that one of the files is <tt>soln.sql</tt>.
This is a solution to the exercise.
BUT try to resist the urge to look at this file before you've
attempted the exercises yourself; you learn by <i>attempting the
exercise</i>, not by simply reading the solution.
<p>
<div class="note">
<b>Tip</b>: Most of the COMP3311 prac exercises require you to play with a number
of files. It would be useful to create a separate subdirectory for
each prac. Trying to maintain a single directory for all pracs will end
up becoming messy, and there's always the danger of over-writing files
with a common name (e.g. <tt>schema.sql</tt>).
</div>

<p><li>
<p><b>Create a new database to hold the company information.</b></p>
<p>
Login to <tt>vxdb02.cse.unsw.edu.au</tt>, start your PostgreSQL server,
and use the <tt>createdb</tt> command to make a new database called
<tt>company</tt>.
More details on <tt>createdb</tt> can be found in the
<a href="<?=$PGDOC?>/app-createdb.html">PostgreSQL Manual</a>.
</p>
<div class="note">
<b>Tip</b>: the <tt>vxdb02</tt> host is supposed to be dedicated
entirely to running servers, such as PostgreSQL. It cannot
also cope with hundreds of large applications like web browsers.
The <b>only</b> things you need to do on <tt>vxdb02</tt> are:
start/stop the PostgreSQL server and run the
<tt>psql</tt> client.
A useful way to set things up for your prac session is:
<ul>
<li> run a terminal app on <tt>vxdb02</tt> for <tt>psql</tt>
<li> run an editor (e.g. emacs, kate, ...) on the local workstation
<li> run a web browser on the local workstation to read this prac exercise
<li> in the editor and psql terminals, change into the same prac directory
<li> edit in the local window; test your SQL by pasting into the <tt>vxdb02</tt> window
</ul>
</div>

<p><li>
<p><b>Load the schema into the database.</b></p>
<p>
In your <tt>vxdb02</tt> window, run the following command to
load the schema:
</p>
<pre>
$ <b>psql company -f schema.sql</b>
</pre>
<p>
<div class="note">
<b>Reminder:</b> the <tt>$</tt> represents the prompt from the
Unix command interpreter (shell). You should not type it. What you
are supposed to type is shown in <tt><b>bold</b></tt>.
Similarly, when we show what commands you are supposed to use with
<tt>psql</tt>, the <tt>psql</tt> prompt will be displayed
as <tt>company=#</tt> and anything you are supposed to type
will be shown in <tt><b>bold</b></tt>.
</div>
<p>
This should produce precisely four <tt>CREATE TABLE</tt> messages.
As long as there are no messages containing <tt>ERROR</tt> or
<tt>FATAL</tt>, things are working as planned.
If there <em>are</em> error messages, <b>read</b> them, <b>think</b>
about them, and try to work out what went wrong.
</p>
<p>
If you don't understand the meaning of the <tt>-f</tt> option,
more details on <tt>psql</tt> can be found in the
<a href="<?=$PGDOC?>/app-psql.html">PostgreSQL Manual</a>.
</p>
<p>
What this command does is to load a copy of the relational schema from
the file <tt>schema.sql</tt> into PostreSQL's catalog. It also
creates an empty instance of each table in the schema. You can examine
the loaded schema via <tt>psql</tt>.
</p>
<p>
Just for fun, try to load the schema again by running the command:
</p>
<pre>
$ <b>psql company -f schema.sql</b>
</pre>
<p>
<p>
This time you should get a bunch of <tt>ERROR</tt> messages 
complaining about tables that already exist.
</p>
<p>
Note that <tt>schema.sql</tt> is a file containing a sequence of
SQL statements (and some comments). One way of executing these
statements on a database is via the <tt>-f</tt> command line option,
as we have shown above.
An alternative way of achieving the same effect is to <q>log in</q>
to the database and invoke the file from within <tt>psql</tt>:
</p>
<pre>
$ <b>psql company</b>
<span class="comment"> You should see PostgreSQL's intro message </span>
<span class="comment"> and then a prompt containing the database name</span>
company=# <b>\i schema.sql</b>
<span class="comment"> ... which will give the same errors as above if you've already loaded schema.sql</span>
</pre>
<p>
<p>
You can examine the schema by connecting to the database in an
interactive <tt>psql</tt> session:
</p>
<pre>
$ <b>psql company</b>
</pre>
<p>
<p>
and then using <tt>psql</tt>'s meta-commands for studying the
catalog.
Take a look at the description of <tt>psql</tt> in the
<a href="<?=$PGDOC?>/app-psql.html">PostgreSQL Manual</a>
for details on the wide range of meta-commands available.
</p>
<p>
For this exercise, the most useful one is <tt>\d</tt> which
allows you to get a list of tables, as well as examine individual
tables.
</p>
<p>
Try the following commands in <tt>psql</tt>:
</p>
<pre>
company=# <b>\d</b>
<span class="comment"> Shows a list of tables</span>

company=# <b>\d Employees</b>
<span class="comment"> Shows a schema for the Employees table</span>

company=# <b>select * from Employees;</b>
<span class="comment"> Shows any tuples in the Employees table</span>
</pre>
<p>
<p>
After each command, try to explain precisely what you observed.
Also, try variations on these commands (e.g. a different table).
</p>
<div class="note">
<b>Reminder:</b> some students get confused about what to type where,
and end up typing SQL commands to the Unix shell, or typing Unix
commands to the PostgreSQL interpreter. If you see a prompt like
<tt>$</tt> or <tt>%</tt> or <tt>vxdb$</tt>, then you're talking to
the Unix shell and SQL commands won't work (will give you some kind
of <q>not found</q> message). If you see a prompt like <tt>company=#</tt>
or <tt>company=(</tt>, etc., then you're talking to the <tt>psql</tt>
system for interacting with a database, and SQL commands will work,
along with other commands like <tt>\d</tt>, etc.
</div>

<li>
<p><b>Load the valid data into the schema.</b></p>
<p>
In your <tt>vxdb02</tt> window, run the following command to
populate the database:
</p>
<pre>
$ <b>psql company -f data.sql</b>
</pre>
<p>
<p>
This will produce a bunch of lines of the form:
</p>
<pre>
INSERT <i>number</i> 1
</pre>
<p>
<p>
The <tt>1</tt> tells you that one tuple was inserted.
The way our PostgreSQL servers on <tt>vxdb02</tt>
are configured,
<tt><i>number</i></tt> will always be zero.
</p>
<div class="note">
<p>
If the PostgreSQL server was configured differently,
you might see a unique number each
time, which would be the
object identifier (oid) of the tuple that was just inserted.
PostgreSQL assigns a unique identifier to each object (tuple,
table, view, ...) in the system.
Despite the fact that object identifiers are not particularly
useful at the user level, PostgreSQL tells you all of the tuple
object id's anyway.
</p>
</div>
<p>
You can then return to an interactive <tt>psql</tt> session
to examine the data in the database using SQL queries, such as:
</p>
<pre>
select * from Employees;
select count(*) from Departments;
</pre>
<p>
<p>
See if you can answer the following questions using SQL:
</p>
<ul>
<li> Which employee works the longest hours each week?
<li> What is the family name of the manager of the Sales department?
<li> How many hours per week does each employee spend in each department?
</ul><br/>
<p>
There's no need to write complex SQL queries to answer the above;
just use simple SQL queries like those above to examine the tables
and work out the answers manually.
</p>

<li>
<p><b>Load the invalid data into the schema.</b></p>
<p>
In your <tt>vxdb02</tt> window, run the following command to
populate the database some more:
</p>
<pre>
$ <b>psql company -f bad.sql</b>
</pre>
<p>
<p>
This will produce the same response as before (<tt>INSERT</tt>
lines), and add some more tuples into the database.
The problem is ... all of the new tuples that were added from the
<tt>bad.sql</tt> file are invalid in some way ... the database
is now full of junk data,
which you can go and examine via <tt>psql</tt> if you want.
</p>
<p>
How could the database system let us insert invalid data?
Because we didn't specify any constraints on what the data
should be like. Take another look at the schema and see how
simple it is; it says nothing about primary keys, foreign keys,
or more fine-grained descriptions of the actual data values.
</p>
<p>
Since the database is now full of junk, you may as well remove
it and start again. Use the <tt>dropdb</tt> command to do
this. Once again, the details of this command are available in the
<a href="<?=$PGDOC?>/app-dropdb.html">PostgreSQL Manual</a>
</p>

<p><li>
<p><b>Add constraints into the schema.</b></p>
<p>
One of the most powerful aspects of database management systems
is that they can help to protect you from putting invalid data
into your database by checking constraints when each new tuple
is added. Of course, they can't work out the constraints by
themselves (if they could, we wouldn't need database programmers
and we wouldn't be running this course). You need to define the
constraints as part of the database schema.
Details about constraints
are available in the <a href="<?=$PGDOC?>/ddl-constraints.html">PostgreSQL Manual</a>
</p>
<p>
The original version of the company schema contains no constraints
at all, apart from very simple domain constraints such as:
</p>
<ul>
<li> the <tt>Employees.hoursPweek</tt> attribute must be a floating
point number,
<li> <tt>Employees.givenName</tt> is a string of up to 30 characters in length
<li> an <tt>Employees</tt> tax file number is an 11-character string
</ul><br/>
<p>
You should now think about what constraints need to be added to the
schema in order to ensure that invalid tuples will be prevented from
being inserted into the database.
</p>
<p>
Some of the missing constraints should be obvious to you from
your understanding of how to map ER designs to relational schemas
(e.g. missing primary key and foreign key constraints).
The domain and <q>common-sense</q> constraints that we require
you to add into this system are:
</p>
<ul>
<li> all TFN's are of the form '<i>ddd-ddd-ddd</i>', where each <i>d</i>
	represents a single digit (Take a look at the
	<a href="<?=$PGDOC?>/functions-matching.html">PostgreSQL Manual</a> for details on Pattern Matching and Regular Expression)
<li> every person has a given name, but may not have a family name
	(e.g. <q>Prince</q>)
<li> nobody can work more hours per week than there are hours in a week
	(each week has 7*24 = 168 hours)
<li> it is meaningless to work negative hours per week
<li> all Departments codes consist of exactly three digits
<li> two Departments cannot have the same name or the same manager
<li> the percentage of time that an employee works in a department must
	be greater than zero
<li> an employee may spend up to and including 100% of their time in a
	given department
</ul><br/>
<p>
Modify the <tt>schema.sql</tt> file and add constraint definitions
all of the above (including primary key, foreign key and constraints to
handle total participation).
</p>
<p>
Once upon a time (version 8.0), creating new databases on PostgreSQL
was a very lightweight process. This may still be true on some
installations of PostgreSQL (such as the one on <tt>vxdb02</tt>)
but does not seem to be true on all PostgreSQL installations. them.
If creating databases is fast on your PostgreSQL server, you can use the
following approach to iteratively check/load your new constraint-rich schema:
</p>
<pre>
$ <b>createdb company</b>
$ <b>psql company -f schema.sql</b>
<span class="comment"> Produces error messages.</span>
<span class="comment"> Fix schema definition using editor in other window.</span>
$ <b>dropdb company</b>
$ <b>createdb company</b>
$ <b>psql company -f schema.sql</b>
...
</pre>
<p>
<p>
If creating databases is slow in your PostgreSQL installation,
you may find that the above approach wastes too much of your time.
To help get things done quicker, there is a small SQL script called
<a href="drop.sql">drop.sql</a> that you can use to clean out all
of the tables from the database and effectively <q>start again</q>
with a fresh database. This means that the way to iterate towards
a solution is something like:
</p>
<pre>
$ <b>createdb company</b>   <span class="comment">... You only need to do this once</span>
$ <b>psql company</b>
...
company=# \i schema.sql
<span class="comment"> Produces notices about creating tables, etc.</span>
<span class="comment"> along with error messages if there are problems with your schema definition.</span>
company=# \i drop.sql
<span class="comment"> Produces a bunch of DROP TABLE messages</span>
<span class="comment"> May also produce ERRORS if some tables weren't created above</span>
<span class="comment"> These ERRORS can obviously be ignored</span>
company=# \i schema.sql
<span class="comment"> Produces notices about creating tables, etc.</span>
<span class="comment"> along with error messages if there are problems with your schema definition.</span>
company=# \i drop.sql
...
<span class="comment"> Continue like this until the schema loads successfully</span>
<span class="comment"> i.e. until \i schema.sql produces no ERROR messages</span>
</pre>
<p>
<p>
<div class="note">
<b>Tip</b>: Because of the way PostgreSQL's parser works, it is sometimes
not possible for it to tell you <em>precisely</em> where an error has
occurred when you're loading schemas.
Sometimes, the line numbers in the error messages refer to the line
immediately after the line containing the error.
In other cases, they refer to the end of the table definition (which
indicates simply that there was something wrong in the table definition).
Occasionally, they will actually pinpoint the exact line where the error
occurred, but mostly it is one of the previous two cases.
Use the syntax diagrams from the PostgreSQL manual to work out
exactly what you've done wrong if you get errors.
This aspect is not a bug in PostgreSQL; it's simply a fact of life with
Yacc-based parsers (see COMP3131 for details).
</div>
</center>

<p><li>
<b>Load the valid data into the new database.</b>
<p>
Once you have successfully loaded the schema,
run the following command to populate the database:
<pre>
$ <b>psql company</b>
  ...
company=# <b>\i data.sql</b>
</pre>
<p>
<p>
This worked ok before, when there was no constraint checking,
but you may be distressed to find that it now generates errors.
Think about the dependencies between tables and work out how to
rearrange the statements in the <tt>data.sql</tt> so that the
data can load ok.
<p>
One way to approach this task would be to follow this sequence of
steps until you get all the data loaded successfully:
</p>
<pre>
$ <b>createdb company</b>
$ <b>psql company -f schema.sql</b>
$ <b>psql company -f data.sql</b>
<span class="comment"> Produces error messages.</span>
<span class="comment"> Fix data.sql using editor in other window.</span>
$ <b>dropdb company</b>
$ <b>createdb company</b>
$ <b>psql company -f schema.sql</b>
$ <b>psql company -f data.sql</b>
...
</pre>
<p>
<p>
As noted above, this approach is too slow on some PostgreSQL installations,
so you can make use of a file <a href="clean.sql">clean.sql</a>
which removes all of the data from the database, leaving just four
<em>empty</em> tables:
</p>
<pre>
$ <b>psql company</b>
...
company=# <b>\i data.sql</b>
<span class="comment"> If it produces error messages,</span>
<span class="comment"> fix data.sql using editor in other window</span>
company=# <b>\i clean.sql</b>
<span class="comment"> Produces messages about deleting tuples</span>
company=# <b>\i data.sql</b>
<span class="comment"> Repeat until this step produces no errors</span>
...
</pre>
<p>
<p>
Once you've loaded the valid data successfully, you're ready to
deal with the invalid data.
</p>
<p>
<b>Side note:</b>
Try changing the order of the <tt>delete</tt> statements above.
What happens? Can you explain why?
<p>
<b>Side note #2:</b>
You might like to think about the difference between
<a href="drop.sql">drop.sql</a> and <a href="clean.sql">clean.sql</a>.
The first completely removes all of the tables from the database.
The second removes all of the tuples and leaves four empty tables.

<p><li>
<b>Reject attempts to insert invalid data.</b>
<p>
With all of the valid data still intact, you should try to
insert the invalid data via the following command:
</p>
<pre>
$ <b>psql company</b>
...
company=# <b>\i bad.sql</b>
</pre>
<p>
<p>
Since every tuple in <tt>bad.sql</tt> is invalid in some way
(assuming that <tt>data.sql</tt> has already been loaded),
you should see <b>only</b> <tt>ERROR</tt> messages.
If you see any <tt>INSERT</tt> messages, then your constraints
are not correct.
</p>
<p>
Repeat the following steps until you finally achieve rejection of
all of the invalid tuples:
</p>
<pre>
$ <b>psql company</b>
...
company=# \i schema.sql
<span class="comment"> Produces notices about creating tables, etc.</span>
company=# \i data.sql
<span class="comment"> Produces INSERT messages; loads valid data</span>
company=# \i bad.sql
<span class="comment"> If it produces any INSERT messages, your schema is</span>
<span class="comment"> incorrect, so you should use a text editor to change schema.sql</span>
company=# \i drop.sql
<span class="comment"> Produces DROP TABLE messages; leaves empty database</span>
company=# \i schema.sql
<span class="comment"> Produces notices about creating tables, etc.</span>
company=# \i data.sql
<span class="comment">  Produces INSERT messages; loads valid data</span>
company=# \i bad.sql
...
<span class="comment"> Continue like this until your schema is correct</span>
<span class="comment"> i.e. until you receive only ERROR messages from \i bad.sql</span>
</pre>
<p>
<p>
Once the output from the <tt>psql</tt> command
</p>
<pre>
company=# <b>\i bad.sql</b>
</pre>
<p>
<p>
consists entirely of error messages (no inserts), you have provided
sufficient constraints to ensure that only valid data can be inserted,
and your prac exercise is complete.

<p><li>
<b>Challenge: tricky constraints #1</b>
<p>
Here's something to think about if you found the above exercise too easy.
<p>
<b>Exercise:</b>
Consider how you might implement the following constraints:
<ul>
<li> no worker can have more than 100% of their time allocated
<li> a manager must spend all of their time in just one department
</ul><br/>
<p>
To test these out you'll need to try to insert additional tuples
that violate these constraints. For the first case, you could use
the following insertion:
<pre>
insert into WorksFor values ('747-400-123','001',10);
</pre>
<p>
For the second case, I'll leave it for you to work out a suitable test.
<p>
Hint: you'll need to use
<a href="<?=$PGDOC?>/plpgsql.html">PLpgSQL</a>
and
<a href="<?=$PGDOC?>/triggers.html">triggers</a>
which we'll discuss in lectures in a few weeks.

<p><li>
<b>Challenge: tricky constraints #2</b>
<p>
Consider a variation on the above ER design, where each employee
works for exactly one department:
<center>
<table border='1' cellpadding='10' cellspacing='0'><tr><td>
<img src='Pic/company2.png'>
</td></tr></table>
</center>
<p>
Think about the changes that this would make to the relational schema.
In particular, now that the relationship between <tt>Employees</tt>
and <tt>Departments</tt> is n:1 rather than n:m, the <tt>WorksFor</tt>
table would be replaced by a non-null foreign key in the <tt>Employees</tt>
table (non-null since every employee <em>must</em> work for one department).
<p>
Modify your schema so that it correctly implements the new ER model
and then try to insert some data.
If you made the modifications correctly, you'll discover that you have
a problem ... You cannot insert an <tt>Employees</tt> tuple until there
is a <tt>Departments</tt> tuple for them to be associated with.
However, you cannot insert any <tt>Departments</tt> tuples until you
have an <tt>Employees</tt> tuple available to be the manager of the
department.
<p>
How can this be resolved?
<p>
There are two possible approaches:
<ul>
<p><li>
remove (some of) the constraints associated with the
<tt>Employees.worksFor</tt> attribute or the
<tt>Departments.manager</tt> attribute.
This would allow you to insert either <tt>Employees</tt> tuples
or <tt>Departments</tt> tuples without insisting on the existence
of the other.
Of course, doing this would mean that you've removed some of the
semantics implied by the ER model (employees <em>must</em> work
for some department and departments <em>must</em> have a manager).
Once the database is populated, you could add the constraints in
via the <a href="<?=PGDOC?>/sql-altertable.html">alter table</a>
command, but any invalid data that you'd already added would remain.
Also, if you ever needed to add a new department which is managed
by a new employee, you'd need to remove the constraints again, make
the additions to the database, and then restore the constraints.
<p><li>
A better approach is to realise that the insertion of a new employee
and a new department at the same time needs to be treated as a single
operation, and that constraint checking simply needs to be delayed
until both tuples are inserted. If the coinstraints are satisfied
with both tuples added, then the operation is successful. If the
constraints are not satisfied, then both tuples should be removed.
The approach of treating multiple updates as a single operation is
known as a transaction.
<br>
PostgreSQL has a method for specifying that constraint checking should
be delayed to the end of a transaction which is described in the section
on <tt>deferrable</tt> constraints in the documentation on the
<a href="<?=$PGDOC?>/sql-createtable.html">create table</a>
statement, and with additional explanation in the
<a href="<?=$PGDOC?>/sql-set-constraints.html">set constraints</a>
documentation.
You'll also need to read the doumentation on
<a href="<?=$PGDOC?>/sql-begin.html">begin</a>
and
<a href="<?=$PGDOC?>/sql-commit.html">commit</a>
to work out how to implement it.
(You can also find an explanation of this in the last couple of
questions in Exercises 03).
</ul><br/>
<p>
<b>Exercise:</b>
Try to implement both of these schemes for handling "mutually dependent"
foreign key constraints.
</ol>

<?=endPage()?>
