<?php
require("../../course.php"); $exID = "03";
$exWeb = WEBHOME."/pracs/$exID"; $exDir = HOMEDIR."/pracs/$exID";
$PGDOC = "https://www.postgresql.org/docs/12/";
echo startPage("Prac Exercise $exID","","SQL Queries, Views, and Aggregates (i)");
echo updateBlurb();
?>

<h3>Aims</h3>

This exercise aims to give you practice in:
<ul>
<li> asking SQL queries on a relatively simple schema
<li> using SQL aggregation and grouping operators
<li> writing SQL view definitions
<li> porting SQL across multiple database management systems
</ul>
This exercise will <b>not</b> explain how
to do everything in fine detail. Part of the aim of the exercise is that you
explore how to use the PostgreSQL and SQLite systems.
The documentation for these systems contains much useful information:
<a href="<?=$PGDOC?>/index.html">PostgreSQL Manual</a>,
<a href="http://www.sqlite.org/docs.html">SQLite Manual</a>.
You should become familiar with where to find useful information in
the documentation ASAP; you will need to know how to use PostgreSQL
and SQLite for the assignments and the exam.

<h3>Background</h3>
<p>
Access logs for web servers contain a considerable amount of information
that is potentially useful in tuning both the web server parameters and
the applications that run on the web server.
A web server access log contains one entry for each page that is
fetched from that server, where a page may be an HTML document, a 
PHP script, an image, etc.
Each log entry contains information about one page access, including:
</p>
<ul>
<li> the IP address of the host from which the page request was made
<li> the precise date/time that the request was made
<li> the URL that was requested (path name relative to server document root)
<li> the status of the fetch operation (e.g. 200 = successful, 404 = not found)
<li> the number of bytes of data transferred to the requestor
</ul>
<p>
Here is an example from the start of the March 2005 log from the Apache
web server on the CSE server <tt>mahler</tt>:
</p>
<pre style="font-size:75%">
60.240.97.148 - - [01/Mar/2005:00:00:00 +1100] "GET /webcms/intro/view_intro.phtml?cid=845&color=%23DEB887 HTTP/1.1" 200 342
60.240.97.148 - - [01/Mar/2005:00:00:03 +1100] "GET /webcms/notice/view_notice.phtml?cid=845&color=%23DEB887&state=view HTTP/1.1" 200 3642
60.229.57.188 - - [01/Mar/2005:00:00:06 +1100] "GET /webcms/creation/index.phtml?tid=000000124004 HTTP/1.1" 200 881
60.229.57.188 - - [01/Mar/2005:00:00:06 +1100] "GET /webcms/login/invalid.phtml HTTP/1.1" 200 1401
60.229.57.188 - - [01/Mar/2005:00:00:07 +1100] "GET /webcms/login/login.phtml HTTP/1.1" 200 4883
60.229.57.188 - - [01/Mar/2005:00:00:09 +1100] "POST /webcms/login/log_in.phtml HTTP/1.1" 302 5
60.229.57.188 - - [01/Mar/2005:00:00:09 +1100] "GET /webcms/creation/index.phtml?tid=000000124013 HTTP/1.1" 200 720
60.229.57.188 - - [01/Mar/2005:00:00:09 +1100] "GET /webcms/creation/menu.phtml?tid=000000124013 HTTP/1.1" 200 1898
60.229.57.188 - - [01/Mar/2005:00:00:10 +1100] "GET /webcms/creation/welcome.phtml?tid=000000124013 HTTP/1.1" 200 5487
60.229.57.188 - - [01/Mar/2005:00:00:12 +1100] "GET /webcms/course/index.phtml?tid=000000124013&cid=860 HTTP/1.1" 200 806
</pre>
<div class="note">
In some ways, this is also a history lesson.
This is a very old version of WebCMS, possibly the very first version,
running on a server called <tt>mahler</tt> which used run various web
applications which have long since fallen into disuse. Note the use of
the <tt>.phtml</tt> suffix ... seriously old school.
</div>
<p>
Some Web-based applications such as WebCMS introduce the notion of a
<q>session</q>
to a user's interaction with the web server. A user logs in to WebCMS,
performs a series of page accesses (e.g. looks the the lecture notes,
reads the message board, etc) and then logs out. All of these accesses
are tied together by being part of a single user's interaction with
the system. In an older version of WebCMS, sessions were implemented
by passing a session
identifier from one PHP script to the next, and checking this against
a copy of the session identifier stored in the database. Thus, while
the web log itself does not store information about users, it is
possible to track an individual user's access to the system by finding
all of the page accesses that make use of the same session identifier.
</p>
<p>
For the purposes of this exercise, imagine that we are interested in
finding out the typical things that people do in a session with WebCMS.
Some of this we can guess: they check the NoticeBoard, take a look
whether there are any new lecture notes, read the current prac exercise,
etc.
Analysing the actual data in detail allows us to either confirm our
hunches or discover new (unexpected) ways in which people use the
system.
Either way, this information could give us ideas on how to tune the
performance of WebCMS.
</p>
<p>
It is very convenient to do this kind of analysis if the data is
loaded into a relational database system, so the first step is to
put the web log data into a relational form that captures the
essential aspects of WebCMS sessions.
Based on this, it is possible to define a database schema to
represent the data from a WebCMS web log:
</p>
<ul>
<li> the IP address and names of various host computers
<li> information about each session using WebCMS, including
	which host it was from and whether the user actually
	logged out via the WebCMS logout page (if they don't
	logout, their session is eventually timed-out)
<li> the details of each individual page access, including
	the name of the script, the parameters, the access time,
	and the session that the access was part of
</ul>
<p>
We use the following ER design:
</p>
<p>
<center>
<table border='1' cellpadding='10' cellspacing='0'><tr><td>
<img src='Pic/schema.png'>
</td></tr></table>
</center>
</p>
which has been converted to a relational schema.
</p>
<pre>
create table Hosts (
	id          integer,
	ipaddr      varchar(20) unique,
	hostname    varchar(100),
	primary key (id)
);

create table Sessions (
	id          int, 
	host        integer,
	complete    boolean,
	primary key (id),
	foreign key (host) references Host(id)
);

create table Accesses (
	session     int,
	seq         int,
	course      int,
	page        varchar(200),
	params      varchar(200),
	accTime     timestamp,
	nbytes      integer,
	primary key (session,seq),
	foreign key (session) references Session(id)
);
</pre>
<p>
This schema is written in standard SQL, and so will load in any
standard-conforming SQL database management system. In particular,
it will load in both PostgreSQL and SQLite.
</p>
<p>
The web server log for the first three days of March 2005 from the old
WebCMS server was pre-processed to fit the schema above
and is available for you load into a database.
</p>
<p>
The data has been placed into three files, each of which consists
of a large number of SQL insert statements.
</p>
<pre>
-rw-r--r--  1 cs3311  cs3311  5568874 <i>Some Date</i> /home/cs3311/web/24T1/pracs/03/Accesses.sql
-rw-r--r--  1 cs3311  cs3311   172536 <i>Some Date</i> /home/cs3311/web/24T1/pracs/03/Hosts.sql
-rw-r--r--  1 cs3311  cs3311   193407 <i>Some Date</i> /home/cs3311/web/24T1/pracs/03/Sessions.sql
</pre>
<p>
Note that these data files are quite large, so you shouldn't
copy them under your home directory unless you have a large
disk quota.
You can always access them without making copies, by using the full pathnames
on any CSE server (including <tt>d2</tt>).
</p>
</p>
<p>
We have also supplied a copy of the schema to build the database
table structures and a template file for the exercises:
</p>
<pre>
-rw-r--r--  1 cs3311  cs3311      657 <i>Some Date</i> /home/cs3311/web/24T1/pracs/03/schema.sql
-rw-r--r--  1 cs3311  cs3311     2243 <i>Some Date</i> /home/cs3311/web/24T1/pracs/03/weblog.sql
</pre>
<p>
We have created a <b><a href="weblog.zip">ZIP file</a></b> containing
all of the above files for you to download and use for this Prac.
Grab a copy of this if you want to work on this exercise on your
own machine.
</p>
<p>
The first thing to do is to make a directory for this Prac and
extract a copy of the data files into this directory. On the CSE
workstations, you can do this via:
</p>
<pre>
% <b>unzip <?=$exDir?>/weblog.zip</b>
</pre>
<p>
while you're in the directory where you want the files to be located.
</p>
<p>
Alternatively, if you're working on this at home, you should download
the <a href="weblog.zip">weblog.zip</a> file your home
machine and run the command:
</p>
<pre>
% <b>unzip weblog.zip</b>
</pre>
<p>
In the examples below, we assume that you have already done this.
</p>

<h3>Setting up the PostgreSQL database</h3>
<p>
Once you have copies of the schema and data files,
you can setup a PostgreSQL database for this Prac via the following
sequence of commands on your PostgreSQL server on <tt>d2</tt>:
</p>
<pre>
% <b>createdb weblog</b>
CREATE DATABASE
% <b>psql weblog -f schema.sql</b>
  <span class="comment">... should produce CREATE TABLE messages ...</span>
% <b>psql weblog -f Hosts.sql</b>
  <span class="comment">... should produce lots of INSERT messages ...</span>
% <b>psql weblog -f Sessions.sql</b>
  <span class="comment">... should produce lots of INSERT messages ...</span>
% <b>psql weblog -f Accesses.sql</b>
  <span class="comment">... should produce lots of INSERT messages ...</span>
  <span class="comment">... will take around 15-25 seconds to complete ...</span>
</pre>
<p>
Each <tt>INSERT</tt> statement should look like:
</p>
<pre>
INSERT 0 1
</pre>
<p>
The <tt>1</tt> means that one tuple was inserted.
You can insert multiple tuples in a single SQL statement,
so this number could potentially be different to <tt>1</tt>.
Since all of the <tt>INSERT</tt> statements in the SQL files
for this prac insert a sigle tuple, you will only see <tt>1</tt>.
The <tt>0</tt> means that no object ID was generated for the new tuple.
PostgreSQL can generate a unique ID for each tuple if you configure it
to do this.
</p>
<p>
An alternative way of achieving the above is:
</p>
<pre>
% <b>createdb weblog</b>
CREATE DATABASE
% <b>psql weblog</b>
...
weblog=#  <b>\i schema.sql</b>
CREATE TABLE
CREATE TABLE
CREATE TABLE
weblog=#  <b>\i Hosts.sql</b>
<span class="comment">... should produce lots of INSERT messages ...</span>
weblog=#  <b>\i Sessions.sql</b>
<span class="comment">... should produce lots of INSERT messages ...</span>
weblog=#  <b>\i Accesses.sql</b>
<span class="comment">... should produce lots of INSERT messages ...</span>
</pre>
<p>
If you don't want to look at at all of the <tt>INSERT</tt> messages,
and you're using Linux or Mac OSX, then you can do the following:
</p>
<pre>
% <b>createdb weblog</b>
CREATE DATABASE
% <b>psql weblog -f schema.sql</b>
  <span class="comment">... should produce CREATE TABLE messages ...</span>
% <b>psql weblog -f Hosts.sql &gt; .errs 2>&amp;1</b>
  <span class="comment">... INSERT messages are added to file .errs ...</span>
% <b>psql weblog -f Sessions.sql &gt;&gt; .errs 2>&amp;1</b>
  <span class="comment">... INSERT messages are added to file .errs ...</span>
% <b>psql weblog -f Accesses.sql &gt;&gt; .errs 2>&amp;1</b>
  <span class="comment">... INSERT messages are added to file .errs ...</span>
</pre>
<p>
Note that the first loading command has only one <tt>&gt;</tt> to create
the <tt>.errs</tt> file, while the other loading commands use
<tt>&gt;&gt;</tt> to append to the <tt>.errs</tt> file.
A useful command, once you've run the above is:
</p>
<pre>
% <b>grep ERROR .errs</b>
</pre>
<p>
to check for any load-time errors.
If there are any errors (and there shouldn't be), all of the tuples
<em>except</em> the incorrect ones will have been loaded into the
database. Using the line numbers in the error messages, you should
be able to find any erroneous <tt>INSERT</tt> statements and correct
them, and then re-run just those statements.
</p>
<p>
Once the database is loaded, access it via <tt>psql</tt> to check that
everything was loaded ok:
</p>
<pre>
% <b>psql weblog</b>
...
weblog=# <b>\d</b>
         List of relations
 Schema |   Name   | Type  | Owner 
--------+----------+-------+-------
 public | accesses | table | <i>YOU</i>
 public | hosts    | table | <i>YOU</i>
 public | sessions | table | <i>YOU</i>
(3 rows)

weblog=# <b>select count(*) from hosts;</b>
 count 
-------
  2213
(1 row)

weblog=# <b>select count(*) from sessions;</b>
 count 
-------
  4610
(1 row)

weblog=# <b>select count(*) from accesses;</b>
 count  
-------
 54490
(1 row)

weblog=# ...
</pre>
<div class="note">
PostgreSQL supports the notion of a schema as a namespace for holding
table, view, type and function definitions.
The <tt>public</tt> value in the schema column is the name of the default
schema, which is the one we generally use.
</div>
<p>
Note that this is a non-trivial-sized database, and if you are not
careful in how you phrase your queries, they make take quite a while
to be answered.
It might be useful to run your <tt>psql</tt> session with timing
output turned on (use <tt>psql</tt>'s <tt>\timing</tt> command to do this).
If a query takes too long to produce a result, see if you can
phrase it differently to get the same answer, but using less time.
</p>
<p>
Another thing to note: the first time you access a table after
creating it (e.g. to run the above counting queries), the query
may be quite slow. On subsequent accesses to the table, it may
be significantly faster. Try re-running the above queries to
see if you observe this. Can you suggest why this is happening?
</p>

<h3>Setting up the SQLite Database</h3>
<p>
After copying the schema and data files,
you can setup an SQLite database for this Prac
via the following sequence of commands on any of the
CSE workstations
(or on your home machine if it has SQLite installed):
</p>
<pre>
% <b>sqlite3 weblog.db</b>
SQLite version 3.8.7.1 2014-10-29 13:59:56
Enter ".help" for usage hints.
sqlite&gt; <b>.read <?=$exDir?>/schema.sql</b>
sqlite&gt; <b>.read <?=$exDir?>/Hosts.sql</b>
sqlite&gt; <b>.read <?=$exDir?>/Sessions.sql</b>
sqlite&gt; <b>.read <?=$exDir?>/Accesses.sql</b>
  <span class="comment">... will take around 20-30 seconds to complete ...</span>
sqlite&gt; <b>.quit</b>
% 
</pre>
<p>
There are a couple of obvious differences between PostgreSQL
and SQLite on data loading. One is that SQLite doesn't give
feedback for every tuple that's inserted. The other is that
the two systems use different commands:
</p>
<pre>
pgsql=# <b>\i <i>File</i></b>
<span class="comment">... read SQL commands from the file <i>File</i> ...</span>
sqlite&gt; <b>.read <i>File</i></b>
<span class="comment">... read SQL commands from the file <i>File</i> ...</span>
</pre>
<p>
Another difference is that you don't need to explicitly create the
database in SQLite. In the example above, we ran the <tt>sqlite3</tt>
command with an argument referring to a non-existent database file.
After we run the <tt>.read</tt> statements and quit, the
<tt>weblog.db</tt> file has been created and contains the tables and tuples
from the SQL files.
To remove a database: PostgreSQL has a specific <tt>dropdb</tt>
command; for an SQLite database, you simply remove the file.
</p>
<p>
One less obvious difference is that
if there are errors in the <tt>INSERT</tt>s ...
<ul>
<li> PostgreSQL prints an error message for the erroneous <tt>INSERT</tt>, but executes all of the others
<li> SQLite halts execution after the first erroneous <tt>INSERT</tt>
</ul>
<p>
Another clear difference is that SQLite
takes longer to do the insertions than PostgreSQL.
</p>
<p>
There is also a lurking problem with what SQLite has done that we will
consider later.
</p>

<h3>Exercises</h3>
<p>
In the questions below, you are required to produce SQL queries
to solve a range of data retrieval problems on this schema.
For each problem, create a view called <tt>Q</tt><i>n</i>
which holds the <q>top-level</q> SQL statement that produces
the answer (this SQL statement may make use of any views defined
earlier in the Prac Exercise). 
In producing a solution for each problem, you may define as many
auxiliary views as you like.
</p>
<p>
To simplify the process of producing these views, a template
file (<a href="weblog.sql"><tt>weblog.sql</tt></a>) is available.
While you're developing your views, you might find it convenient
to edit the views in one window (i.e. edit the <tt>weblog.sql</tt>
file containing the views) and copy-and-paste the view definitions
into another window running <tt>psql</tt> or <tt>sqlite3</tt>.
</p>
<p>
Note that the order of the results does not matter (except for the
examples where you are imposing an order using <tt>order by</tt>).
As long as you have the same set of tuples, your view is correct.
Remember that, in theory, the output from an SQL query is a set.
</p>
<p>
Once you have completed each of the view definitions, you can
test it simply by typing:
</p>
<pre>
weblog=# <b>select * from Q<i>n</i>;</b>
</pre>
<p>
and observing whether the result matches the expected result
given below.
</p>

<h3>Queries in PostgreSQL</h3>
<ol start='1'>
<li>
<p>
How many of the page accesses occurred on March 2?
</p>
<p>
The results should look like:
</p>
<pre>
   weblog=# <b>select * from Q1;</b>
    nacc  
   -------
    20144
   (1 row)
</pre>
<li>
<p>
How many times was the main WebCMS MessageBoard search facility used?
You can recognise this by the fact that the page contains <tt>messageboard</tt>
and the parameters string contains <tt>state=search</tt>.
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q2;</b>
 nsearches 
-----------
         0
(1 row)
</pre>
<p>
(Note: if you get 1 as the count, you're probably picking up a search
on one of the Web<b>G</b>MS messageboards, which is not a usage of
the main WebCMS MessageBoard.)
</p>
<li>
<p>
On which (distinct) machines in the Tuba Lab were WebCMS sessions run
that were not terminated correctly (i.e. were uncompleted)?
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q3;</b>
             hostname             
----------------------------------
 tuba00.orchestra.cse.unsw.edu.au
 tuba04.orchestra.cse.unsw.edu.au
 tuba05.orchestra.cse.unsw.edu.au
 tuba06.orchestra.cse.unsw.edu.au
 tuba07.orchestra.cse.unsw.edu.au
 tuba16.orchestra.cse.unsw.edu.au
 tuba18.orchestra.cse.unsw.edu.au
 tuba20.orchestra.cse.unsw.edu.au
 tuba21.orchestra.cse.unsw.edu.au
(9 rows)
</pre>
<p>
(Hint: the <tt>Sessions.complete</tt> attribute tells you whether
a given session was completed)
</p>
<li>
<p>
What are the minimum, average and maximum number of bytes
transferred in a single page access?
Produce all three values in a single tuple, and make sure
that they are all integers.
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q4;</b>
 min | avg  |  max   
-----+------+--------
   0 | 3412 | 425253
(1 row)
</pre>
<li>
<p>
How many of the sessions were run from CSE hosts?
A CSE host is one whose host name ends in <tt>cse.unsw.edu.au</tt>.
Ignore any machines whose hostname is not known.
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q5;</b>
 nhosts 
--------
   1380
(1 row)
</pre>
<li>
<p>
How many of the sessions were run from non-CSE hosts?
A non-CSE host is one whose host name does not end in
<tt>cse.unsw.edu.au</tt>.
Ignore any machines whose hostname is not known.
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q6;</b>
 nhosts 
--------
   2785
(1 row)
</pre>
<li>
<p>
How many page accesses were there in the longest session?
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q7;</b>
 session | length 
---------+--------
    2945 |    576
(1 row)
</pre>
<li>
<p>
Each <tt>Accesses</tt> tuple indicates an access to a single WebCMS
page/script.
Produce a list of pages and their access frequency (i.e. how
many times each is accessed).
<b>Do not</b> use <q><tt>order by</tt></q> or
<q><tt>limit</tt></q> in the view.
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q8 order by freq desc limit 10;</b>
              page              | freq 
--------------------------------+------
 notice/view_notice             | 9707
 course/index                   | 9288
 course/menu                    | 9133
 lecture/view_lecture           | 2969
 intro/view_intro               | 1627
 class/view_class               | 1303
 webgms/group/view_group        | 1205
 lab/view_lab                   | 1047
 messageboard/view_messagetopic |  735
 webgms/overview/view_intro     |  692
(10 rows)
</pre>
<li>
<p>
WebCMS is divided into modules, where the PHP scripts for each module
is contained in a subdirectory. We can work out the module name by
looking at the first component of the script name (e.g. in the sample
output above, <tt>notice</tt>, <tt>course</tt>, <tt>lecture</tt>, etc.
are modules).
Produce a table of modules and their access frequency (i.e. how
many times each is accessed).
<b>Do not</b> incorporate <q><tt>order by</tt></q> or
<q><tt>limit</tt></q> into the view.
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q9 order by freq desc limit 10;</b>
    module    | freq  
--------------+-------
 course       | 18602
 notice       |  9859
 webgms       |  8122
 lecture      |  3903
 messageboard |  2354
 creation     |  1884
 login        |  1776
 intro        |  1720
 class        |  1375
 lab          |  1216
(10 rows)
</pre>
<p>
<b>Hint:</b> you'll need to find out more about PostgreSQL
<a href='<?=$PGDOC?>/functions-string.html'>string operators</a>
and
<a href='<?=$PGDOC?>/functions-matching.html#FUNCTIONS-POSIX-REGEXP'>regular expressions</a>.<br>
<b>Note:</b> not all page URLs contain the '/' character;
a page URL that looks simply like <tt>'lecture'</tt> should be treated
as a reference to the lecture module.
</p>
<li>
<p>
The script that maps the web log into relational tuples isn't perfect.
Has it produced any sessions that have no corresponding accesses?
Write a view to print the session IDs of any such sessions.
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q10;</b>
 session 
---------
    3992
    3998
    4610
(3 rows)
</pre>
<li>
<p>
Which hosts were not involved with any accesses to WebCMS?
</p>
<p>
The result should look like:
</p>
<pre>
weblog=# <b>select * from Q11;</b>
              unused               
-----------------------------------
 tm.net.my
 203.199.51.148.static.vsnl.net.in
 mahler.cse.unsw.edu.au
(3 rows)
</pre>
</ol>
<p>
You should attempt the above exercises before looking at the
<a href="soln1.sql">PostgreSQL sample solutions</a>.
</p>

<h3>Queries in SQLite</h3>
<p>
Now that you've attempted the above exercises in PostgreSQL,
let's consider how things would work in SQLite.
This provides an interesting exercise in SQL portability,
since both databases support "standard SQL".
<p>
<p>
Make a copy of your <tt>weblog.sql</tt> file and start testing
the views that you created for PostgreSQL for SQLite.
</p>
<div class="note">
Note that the user interface for <tt>sqlite3</tt> is not
as friendly as <tt>psql</tt>.
It doesn't format its query results quite as nicely; to get
something vaguely similar to <tt>psql</tt>, you'll need to
run the commands:
</p>
<pre>
sqlite&gt; <b>.headers on</b>
sqlite&gt; <b>.mode column</b>
</pre>
<p>
Also, it doesn't support paginated output for long query results.
To achieve this, you can send the query output to a file and scan
the file page-by-page.
An alternative is to use an SQLite gui, rather than the <tt>sqlite3</tt>
shell.
And, finally, its up-arrow command history feature goes backwards
line-by-line, rather than statement-by-statement as <tt>psql</tt>
does.
</div>
</p>
<p>
The first problem you will notice is that SQLite doesn't support
</p>
<pre>
create or replace view <i>ViewName</i> as ...
</pre>
<p>
The fix for this is simple enough. Change all of the view definitions
to something like:
</p>
<pre>
drop view if exists <i>ViewName</i>;
create view <i>ViewName</i> as ...
</pre>
<p>
Another problem relating to view definitions is that SQLite does
not support the naming of view attributes in the header of the
<tt>create view</tt> statement.
PostgreSQL allows you to define views as:
</p>
<pre>
create view <i>ViewName</i>(<i>attr<sub>1</sub></i>, <i>attr<sub>2</sub></i>, ...)
as
select <i>expr<sub>1</sub></i>, <i>expr<sub>2</sub></i>, ...
</pre>
<p>
This is just a convenient (but standard) shorthand for:
</p>
<pre>
create view <i>ViewName</i>
as
select <i>expr<sub>1</sub></i> as <i>attr<sub>1</sub></i>, <i>expr<sub>2</sub></i> as <i>attr<sub>2</sub></i>, ...
</pre>
<p>
and so you will need to change all of the view definitions to this
form. (If you need a hint, some of the view definitions in the
PostgreSQL solutions already use this form.)
</p>
<p>
Once you've made the above changes, many of the views will work
correctly in both PostgreSQL and SQLite.
</p>
<p>
The next problem you will notice is that the "obvious" solution
for view <tt>Q3</tt> doesn't seem to work. It produces <em>all</em>
of the workstations rather than just the ones where sessions were
not terminated correctly.
The problem here is that SQLite doesn't support distinguished
boolean values like <tt>true</tt> or <tt>'t'</tt> and <tt>false</tt>
or <tt>'f'</tt>.
It supports the syntax for a <tt>boolean</tt> data type, but
actually uses 0 for <tt>false</tt> and 1 for <tt>true</tt>.
Since these values are written into the <tt>Sessions.sql</tt>
file, the only way to fix the problem is to modify this file
and then rebuild the <tt>weblog.db</tt> database.
</p>
<p>
(As an aside: can you explain why the query returns all
workstations on the original database, with <tt>'t'</tt>
and <tt>'f'</tt> rather than <tt>1</tt> and <tt>0</tt>?)
</p>
<p>
Finally, while you are trying to write a solution for view
<tt>Q9</tt>, you will discover that SQLite's string
processing functions aren't quite as powerful as PostgreSQL's.
As far as I can tell, there is no way to solve <tt>Q9</tt> in
SQLite without re-doing the database structure and separating
out the module name as a separate attribute in the table.
</p>
<p>
The goal now is to get as many queries as you can to work on
both systems and to understand why some queries can't be made
to work with the existing data.
</p>
<p>
Once you've attempted the exercises, compare your solutions
against those in the <a href="soln2.sql">SQLite sample solutions</a>.
</p>
</body>
</html>
