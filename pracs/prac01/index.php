<?php
require("../../course.php"); $exID = "01";
$exWeb = WEBHOME."/pracs/$exID"; $exDir = HOMEDIR."/pracs/$exID";
echo startPage("Prac Exercise $exID","","PostgreSQL: a client-server RDBMS");
echo updateBlurb();
?>

<h3>Aims</h3>

This exercise aims to get you to:
<ul>
<li> install your PostgreSQL database server at CSE
<li> create, populate and examine a very small database
</ul>
<p>
You ought to get it done by end of Week 2,
since you'll need it to start working on Assignment 1.
</p>

<h3>Background</h3>

<p>
This course uses the PostgreSQL database management system
(<a href="https://www.postgresql.org/">www.postgresql.org</a>),
a powerful, open-source, client-server DBMS.
We are using <a href="https://www.postgresql.org/docs/current/index.html">version 17</a> in this course, but if you have access to a recent version like 14/15/16,
that should be ok.
</p>
<p>
We provide an installed PostgreSQL server in CSE (details
below), and you can do all of your prac work there.
However, it is certainly possible to install PostgreSQL on
your home machine.
There are plenty of online resources describing how to do this for
different operating systems (type "install postgresql" at Google).
</p>
<p>
PostgreSQL runs on most platforms, but the installation instructions
will be different for each one.
There are downloadable binaries for many platforms, but they place
restrictions on how your server is configured.
Other installations come with a GUI front-end; I think this moves you
too far away from the server, and simply gets in the way ... but
maybe that's just me.
</p>
<p>
On Linux and MacOS, I have found it best to install from source code,
which is downloadable via postgresql.org.
The installation is relatively straightforward and you can specify
precisely where on your filesystem you want the PostgreSQL files
to be located.
</p>
<p>
If you don't want to run PostgreSQL on your own machine, this Prac
Exercise describes how to set it up on your CSE account.
You can still work from home by accessing your server through VLab
or <tt>ssh</tt>.
</p>
<p>
The practical work and the assignments can be carried out on
a CSE machine called <code>vxdb02.cse.unsw.edu.au</code>.
You run your own PostgreSQL server on this machine and are
effectively the database administrator of this server.
This machine has been configured to run large numbers* of
PostgreSQL servers.
</p>
<p>
For brevity, we'll refer to <code>vxdb02.cse.unsw.edu.au</code> as
<code>vxdb02</code> in the rest of this document.
</p>
<div class="note">
* Note: "large numbers" is around 400. If you leave your
work to the last minute, and find 600 other students all trying
to run PostgreSQL on <code>vxdb02</code>, performance will be sub-optimal.
Of course, you can avoid this potential bottleneck by
installing and running PostgreSQL on your home machine,
or by finishing your assignments early.
</div>
<p>
You should <i>not</i> use other CSE servers such as <tt>vx03</tt>
for running PostgreSQL; if you do, your PostgreSQL server will
most likely be terminated automatically not long after it starts.
</p>
<div class="note">
Reminder:
You should <em>always</em> test your work on <code>vxdb02</code>
before you submit assignments, since that's where we'll be
running our tests to award your marks.
</div>
<p>
What <tt>vxdb02</tt> provides is a large amount of storage and
compute power that is useful for students studying databases.
You should have access to <code>vxdb02</code> because you're
enrolled in COMP3311.
You can access <code>vxdb02</code> using either
<code>ssh</code> or VLab.
</p>
<p>
In the examples below, we have used the <code>$</code> sign to represent
the prompt from the command interpreter (shell). In fact, the prompt may
look quite
different on your machine (e.g., it may contain the name of the machine
you're using, or your username, or the current directory name).
All of the things that the computer types are in <code>this font</code>.
The commands that <b>you</b> are supposed to type are in
<code><b>this bold font</b></code>.
Some commands use <code>$USER</code> as a placeholder for your
CSE username.
</p>

<h3>Exercises</h3>

<h4>Stage 1: Getting started on <tt style="font-size:110%">vxdb02</tt></h4>

<p>
Log in to <tt>vxdb02</tt>. If you're not logged into
<code>vxdb02</code> nothing that follows will work.
</p>
<p>
You can log into <code>vxdb02</code> from a command-line (shell) window
on any CSE machine (including <tt>vlab</tt>) via the command:
</p>
<pre>
$ <b>ssh vxdb02</b>
</pre>
<p>
<p>
If you're doing this exercise from home, you can use any <code>ssh</code>
client, but you'll need to refer to <code>vxdb02</code> via its fully-qualified
name:
</p>
<pre>
$ <b>ssh <i>YourZID</i>@vxdb02.cse.unsw.edu.au</b>
</pre>
<p>
<p>
From home, an alternative is to use VLab. This requires a VNC client
(e.g. <a href="https://tigervnc.org/">TigerVNC</a>). Use the VNC server
</p>
<pre>
vlab.cse.unsw.edu.au:5920
</pre>
<p>
<p>
You can check whether you're actually logged in to <code>vxdb02</code>
by using the command:
</p>
<pre>
$ <b>hostname</b>
vxdb02
</pre>
<p>
<p>
Your home directory at CSE is directly accessible from
<code>vxdb02</code>. Run the <code>ls</code> command to check
that you are indeed in your CSE home directory.
</p>
<p>
The first time you log in to <code>vxdb02</code>, it automatically
creates a directory to hold your databases:
</p>
<pre>
$ <b>ls -al /localstorage/$USER</b>
</pre>
<p>
<p>
This directory is initially empty, but we're about to put the
files for a PostgreSQL server into it.
</p>

<h4>Stage 2 and 3: The easy way</h4>
<p>
After connecting to <code>vxdb02</code> simply run the following command
<pre>
$ 3311 pginit
</pre>
<p>
<code>3311 pginit</code> will give you a lot of output, <b>you should read all of it</b>
<p>
Assuming there are no errors, you then simply run the command
<pre>
$ source /localstorage/$USER/env
</pre>
<p>
And you are good to go.
<p>
Everything has been setup for you.
<p>
The next time you login to <code>vxdb02</code> all you need to do is run
<pre>
$ source /localstorage/$USER/env
$ p1
</pre>
<p>
<p>
And your database server will start back up again

<h4>Stages 2 and 3: A More detailed way</h4>

<p>
If you want to see more details about how <code>pginit</code>
sets things up, there are extended descriptions of stages 2 and 3 at the end of this document.
</p>

<h4>Stage 4: Using your PostgreSQL Server</h4>

<p>
In this section, we assume that you have completed Stage 3
and now have a directory for PostgreSQL on <code>vxdb02</code>
<p>
When you want to do some work with PostgreSQL: login to <code>vxdb02</code>
set up your environment, start your server, do your work, and then stop the server
before logging off 
</p>
<p>
<span class="red"><b>Do not leave your PostgreSQL server running
while you are not using it.</b></span>
</p>
<p>
A typical session with PostgreSQLwould begin with you logging in to
the <code>vxdb02</code>.
You would then do something like ...
</p>
<pre>
$ <b>source /localstorage/$USER/env</b>
  <span class="comment">... sets up environment ...</span>
$ <b>p1</b>
  <span class="comment">... start the PostgreSQL server ...</span>
$ <b>psql <i>SomeDatabase</i></b>
  <span class="comment">... work with a database ... </span>
$ <b>p0</b>
  <span class="comment">... stop the PostgreSQL server ...</span>
</pre>
<p>
<p>
Each time you want to use your PostgreSQL server, you'll need to do
the following:
</p>
<p>
Note that <code>p1</code> and <code>p0</code> are abbreviations
defined in the <code>env</code>. They invoke the <code>pg_ctl</code>
command which controls the operation of the PostgreSQL server.
</p>
<p>
After using <code>p1</code>, you can check whether your server
is running via the command:
</p>
<pre>
$ <b>psql -l</b>
</pre>
<p>
<p>
Note: <code>l</code> is lower-case L, not the digit 1.
</p>
<p>
Try starting, checking, and stopping the server a few times.
</p>
<p>
Things occasionally go wrong, and knowing how to deal with them will save
you lots of time. There's a discussion of common problems at the end of
this document; make sure that you read and understand it.
</p>
<p>
Once your PostgreSQL server is running, you can access your PostgreSQL
databases via the <code>psql</code> command.
You normally invoke this command by specifying the name of a database,
e.g.
</p>
<pre>
$ <b>psql <i>MyDatabase</i></b>
</pre>
<p>
<p>
If you type <code>psql</code> command without any arguments, it assumes
that you are trying to access a database with the same name as your login
name. Since you probably won't have created such a database, you're likely
to get a message like:
</p>
<pre>
psql: FATAL:  database "<i>${USER}</i>" does not exist
</pre>
<p>
<p>
You will get a message like this any time that you try to access a
database that does not exist.
</p>
<p>
If you're not sure what databases you have created, <code>psql</code>
can tell you via the <code>-l</code> option
</p>
<pre>
$ <b>psql -l</b>
</pre>
<p>
<p><p>
If you run this command now, you ought to see output that looks like:
</p>
<pre>
SET
                                                List of databases
   Name    | Owner | Encoding | Locale Provider | Collate |  Ctype  | ICU Locale | ICU Rules | Access privileges 
-----------+-------+----------+-----------------+---------+---------+------------+-----------+-------------------
 postgres  | jas   | UTF8     | libc            | C.UTF-8 | C.UTF-8 |            |           | 
 template0 | jas   | UTF8     | libc            | C.UTF-8 | C.UTF-8 |            |           | =c/jas           +
           |       |          |                 |         |         |            |           | jas=CTc/jas
 template1 | jas   | UTF8     | libc            | C.UTF-8 | C.UTF-8 |            |           | =c/jas           +
           |       |          |                 |         |         |            |           | jas=CTc/jas
(3 rows)
</pre>
<p><p>
Of course, it will be <i>your</i> username, and not <tt>jas</tt>,
and it will probably look seriously messed up because the lines are
long and may wrap in your average terminal window.
</p>
<p>
Note that PostgreSQL commands like <code>psql</code> and <code>createdb</code>
are a lot noisier than normal Linux commands.
In particular, they all seem to print <code>SET</code> when they
run; you can ignore this.
Similarly, if you see output like <nobr><code>INSERT 0 1</code></nobr>,
you can ignore that as well.
</p>
<p>
The above three databases are created for use by the PostgreSQL server;
you should not modify them.
At this stage, you don't need to worry about the contents of the other
columns in the output.
As long as you see at least three databases when you run the
<code>psql -l</code> command, it means that your PostgreSQL
server is up and running ok.
</p>
<p>
Note that you are the administrator for your PostgreSQL server
(add "database administrator" to your CV) and you can create as many databases as
you like, within the limits of your disk quota.
<p>
From within <code>psql</code>, the fact that you are an administrator
is indicated by a prompt that looks like
</p>
<pre>
<i>dbName</i>=#
</pre>
<p>
<p>
rather than the prompt for ordinary database users
</p>
<pre>
<i>dbName</i>=>
</pre>
<p>
<p>
which you may have seen in textbooks or notes.
</p>
<p>
Note that you can only access databases created as above
while you're logged into <code>vxdb02</code>.
In other words, you must run the <code>psql</code>
command on <code>vxdb02</code>.
</p>
<p>
Note that the <b>only</b> commands that you should run on
<code>vxdb02</code> are the commands to start and stop
the server, the <code>psql</code> command to start an interactive
session with a database, and the other PostgreSQL clients such as
<code>createdb</code>.
Do not run other processes such as web browsers or drawing
programs or games on <code>vxdb02</code>.
Text editors are OK; VScode is not.
</p>
<p>
All of the PostgreSQL client applications are documented in the
<a target="_new" href="http://www.postgresql.org/docs/17/index.html">PostgreSQL manual</a>,
in the "PostgreSQL Client Applications" section.
While there are quite a few PostgreSQL client commands,
<code>psql</code> will be the one that you will mostly use.
</p>
<div class="note">
<p>
<b>Mini-Exercise:</b> a quick way to check whether your PostgreSQL server
is running is to try the command:
</p>
<pre>
$ <b>psql -l</b>
</pre>
<p>
<p>
Try this command now.
</p>
<p>
If you get a response like:
</p>
<pre>
psql: command not found
</pre>
<p>
<p>
then you haven't set up your environment properly; <code>source</code> the
<code>env</code> file.
</p>
<p>
If you get a response like:
</p>
<pre>
psql: could not connect to server: No such file or directory
        Is the server running locally and accepting
        connections on Unix domain socket "....s.PGSQL.5432"?
</pre>
<p>
then the server isn't running.
</p>
<p>
If you get a list of databases, like the example above, then this
means your server is running ok and ready for use.
</p>
</div>
<p>

<h4>Cleaning up</h4>

<p>
After you've finished a session with PostgreSQL,
it's essential that you shut your PostgreSQL server down
(to prevent overloading <code>vxdb02</code>).
Recall that you do this via the command
</p>
<pre>
$ <b>p0</b>
</pre>
<p>
<p>
PostgreSQL generates log files that can potentially
grow quite large. If you start your server using
<code>p1</code>, the log file is called
</p>
<pre>
/localstorage/$USER/pgsql/data/log
</pre>
<p>
<p>
It would be worth checking every so often to see how large
it has become.
To clean up the log, simply stop the server and remove the file.
Note: if you remove the logfile while the server is running, you
may not remove it at all; its link in the filesystem will be gone,
but the disk space will continue to be used and grow until the
server stops.
</p>
<div class="note">
<p>
<b>Mini-Exercise:</b>
Try starting and stopping the server a few times, and running
<code>psql</code> both when the server is running and when it's
not, just to see the kinds of messages you'll get.
</p>
</div>
<p>


<h4>Exercise #1: Making a database</h4>

<p>
Start by logging in to <code>vxdb02</code> and setting the environment.
</p>
<p>
Once the PostgreSQL server is running, try creating a database by
running the command:
</p>
<pre>
$ <b>createdb mydb</b>
</pre>
<p>
<p>
which will create the database, or give an error message if it
can't create it for some reason. (A typical reason for failure would be
that your PostgreSQL server is not running.)
<p>
Now use the <code>psql -l</code> command to check that the new database exists.
</p>
<p>
You can access the database by running the command:
</p>
<pre>
$ <b>psql mydb</b>
</pre>
<p>
<p>
which should give you a message like
</p>
<pre>
SET
psql (17.2)
Type "help" for help.

mydb=# 
</pre>
<p>
<p>
Note that <code>psql</code> lets you execute two kinds of commands:
SQL queries and updates, and <code>psql</code> <q>meta</q>-commands.
The <code>psql</code> <q>meta</q>-commands allow you to examine the
database schema, and control various aspects of <code>psql</code>
itself, such as where it writes its output and how it formats tables.
</p>
<p>
Getting back to the <code>psql</code> session that you just started,
the <code>mydb</code> database is empty, so there's not much you can do
with it.
The <code>\d</code> (describe) command allows you to check what's in
the database.
If you type it now, you get the unsurprising response
</p>
<pre>
mydb=# <b>\d</b>
Did not find any relations.
</pre>
<p>
<p>
About the only useful thing you can do at the moment
is to quit from <code>psql</code> via the <code>\q</code> command.
</p>
<pre>
mydb=# <b>\q</b>
$ ... <i>now waiting for you to type Linux commands</i> ...
</pre>
<p>
<p>
Note: it is common to forget which prompt you're looking at and sometimes
type Linux commands to <code>psql</code> or to type SQL queries to the
Linux shell. It usually becomes apparent fairly quickly
what you've done wrong, but can initially be confusing when you think that
the command/query is not behaving as it should.
Here are examples of making the above two mistakes:
</p>
<pre>
$ ... <i>Linux command interpreter</i> ...
$ <b>select * from table;</b>
-bash: syntax error near unexpected token `from'
$ <b>psql mydb</b>
... <i>change context to PostgreSQL</i> ...
mydb=# <b>ls -l</b>
mydb-# ... <i>PostgreSQL waits for you to complete what it thinks is an SQL query</i> ...
mydb-# <b>;</b>    ... <i>because semi-colon finishes and then executes an SQL query</i> ...
ERROR:  syntax error at or near "ls"
LINE 1: ls -l
        ^
mydb=# <b>\q</b>
$ ... <i>back to Linux command interpreter</i> ...
</pre>
<p>

<h4>Exercise #2: Populating a database</h4>

<p>
Once the <code>mydb</code> database exists, the following command
will create the schemas (tables) and populate them with tuples:
</p>
<pre>
$ <b>psql mydb -f <?=$exDir?>/mydb.sql</b>
</pre>
<p>
<p>
Note that this command produces quite a bit of output, telling you
what changes it's making to the database. The output should look like:
</p>
<pre>
SET
CREATE TABLE
INSERT 0 1
INSERT 0 1
INSERT 0 1
CREATE TABLE
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
CREATE TABLE
INSERT 0 1
INSERT 0 1
INSERT 0 1
CREATE TABLE
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
INSERT 0 1
</pre>
<p>
<p>
The lines containing <code>CREATE TABLE</code> are, obviously, related
to PostgreSQL creating new database tables (there are four of them).
The lines containing <code>INSERT</code> are related to PostgreSQL
adding new tuples into those tables.
</p>
<p>
Clearly, if we were adding hundreds of tuples to the tables, the output
would be very long. You can get PostgreSQL to stop giving you the
<code>INSERT</code> messages by using the <code>-q</code> option to
the <code>psql</code> command.
</p>
<p>
PostgreSQL's output can be verbose during database loading. If you want
to ignore everything <em>except</em> error messages, you could use a
command like:
</p>
<pre>
$ <b>psql mydb -f <?=$exDir?>/mydb.sql 2>&1 | grep ERROR</b>
</pre>
<p>
<p>
If you don't understand the fine details of the above, take a look at the
documentation for the Linux/Unix shell.
</p>
<p>
The <code>-f</code> option to <code>psql</code> tells it to read its input
from a file, rather than from standard input (normally, the keyboard).
If you look in the <a href="mydb.sql"><code>mydb.sql</code></a> file, you'll find a mix of table
(relation) definitions and statements to insert tuples into the database.
We don't expect you to understand the contents of the file at this stage.
</p>
<p>
If you try to run the above command again, you will generate a heap of
error messages, because you're trying to insert the same collection of
tables and tuples into the database, when they've already been inserted.
</p>
<p>
Note that the tables and tuples are now permanently stored on disk.
If you switch your PostgreSQL server off, when you restart it
the contents of the <code>mydb</code> database will be available,
in whatever state you left them from the last time you used the database.
</p>


<h4>Exercise #3: Examining a database</h4>

<p>
One simple way to manipulate PostgreSQL databases
is to use the <code>psql</code> command (which is a "shell"
like the <tt>sqlite3</tt> command in the first prac exercise).
A useful way to start exploring a database is to find out what
tables it has. We saw before that you can do this with the
</code>\d</code> (describe) command. Let's try that on the
newly-populated <code>mydb</code> database.
</p>
<pre>
mydb=# <b>\d</b>
         List of relations
 Schema |   Name    | Type  | Owner 
--------+-----------+-------+-------
 public | courses   | table | $USER
 public | enrolment | table | $USER
 public | staff     | table | $USER
 public | students  | table | $USER
(4 rows)
</pre>
<p>
<p>
You can ignore the <code>Schema</code> column for the time being.
The <code>Name</code> column tells you the names of all tables
(relations) in the current database instance. The <code>Type</code>
column is obvious, and, you may think, unnecessary. It's there because
<code>\d</code> will list all objects in the database, not just tables;
it just happens that there are only tables in this simple database.
The <code>Owner</code> should be your username, for all tables.
</p>
<p>
One thing to notice is that the table names are all in lower-case,
whereas in the <a href="mydb.sql"><code>mydb.sql</code></a> file, they had an initial
upper-case letter.
The SQL standard says that case does not matter in unquoted identifiers
and so <code>Staff</code> and <code>staff</code> and <code>STAFF</code>
and even <code>StAfF</code> are all equivalent.
To deal with this, PostgreSQL simply maps <em>identifiers</em>
into all lower case internally. You can still use <code>Staff</code>
when you're typing in SQL commands; it will be mapped automatically
before use.
</p>
<div class="note">
<p>
There are, however, advantages to using all lower case whenever
you're dealing with <code>psql</code>. For one thing, it means
that you don't have to keep looking for the shift-key. More
importantly, <code>psql</code> provides table name and field name
completion (you type an initial part of a table name, then type
the TAB key, and <code>psql</code> completes the name for you if
it has sufficient context to determine this unambiguously),
but it only works when you type everything in lower case.
The <code>psql</code> interface has a number of other features
(e.g. history, command line editing) that make it very nice to
use.
</p>
</div>
<p> 
If you want to find out more details about an individual table,
you can use:
</p>
<pre>
mydb=# <b>\d Staff</b>
             Table "public.staff"
  Column  |         Type          | Modifiers 
----------+-----------------------+-----------
 userid   | character varying(10) | not null
 name     | character varying(30) | 
 position | character varying(20) | 
 phone    | integer               | 
Indexes:
    "staff_pkey" PRIMARY KEY, btree (userid)
Referenced by:
    TABLE "courses" CONSTRAINT "courses_lecturer_fkey" FOREIGN KEY (lecturer) REFERENCES staff(userid)
</pre>
<p>
<p>
As you can see, the complete name of the table is <code>public.staff</code>,
which includes the schema name. PostgreSQL has the notion of a <q>current
schema</q> (which is the schema called <code>public</code>, by default), and
you can abbreviate table names by omitting the current schema name, which is
what we normally do.
The types of each column look slightly different to what's in the
<code>mydb.sql</code> file; these are just PostgreSQL's internal
names for the standard SQL types in the schema file.
You can also see that the <code>userid</code> field is not allowed to be
null; this is because it's the primary key (as you can see from the index
description) and primary keys may not contain null values.
The index description also tells you that PostgreSQL has built a B-tree
index on the <code>userid</code> field.
</p>
<p>
The final line in the output tells you that one of the other tables in
the database (<code>Courses</code>) has a foreign key that refers to the
primary key of the <code>Staff</code> table, which you can easily see
by looking at the <a href="mydb.sql"><code>mydb.sql</code></a> file.
This is slightly useful for a small database, but becomes extremely
useful for larger databases with many tables.
</p>
<p>
The next thing we want to find out is what data is actually contained in
the tables.
This requires us to use the SQL query language, which you may not know
yet, so we'll briefly explain the SQL statements that we're using, as
we do them.
</p>
<p>
We could find out all the details of staff members as follows:
</p>
<pre>
mydb=# <b>select * from Staff;</b>
  userid  |     name      |    position     | phone 
----------+---------------+-----------------+-------
 jingling | Jingling Xue  | Professor       | 54889
 jas      | John Shepherd | Senior Lecturer | 56494
 andrewt  | Andrew Taylor | Associate Prof  | 55525
(3 rows)
</pre>
<p>
<p>
The SQL statement says, more or less, <q>tell me everything (*)
about the contents of the <code>Staff</code> table</q>.
Each row in the output below the heading represents a tuple
in the table.
</p>
<p>
Note that the SQL statement ends with a semi-colon.
The meta-commands that we've seen previously didn't require this,
but SQL statements can be quite large, and so, to allow you to
type them over several lines, the system requires you to type a
semi-colon to mark the end of the SQL statement.
</p>
<p>
If you forget to put a semi-colon, the prompt changes subtly:
</p>
<pre>
mydb=# <b>select * from Staff</b>
mydb-# 
</pre>
<p>
<p>
This is PostgreSQL's way of telling you that you're in the middle
of an SQL statement and that you'll eventually need to type a semi-colon.
If you then simply type a semi-colon to the second prompt, the
SQL statement will execute as above.
</p>
<div class="note">
<b>Mini-Exercise</b>: find out the contents of the other tables.
<p>
Here are some other SQL statements for you to try out. You don't need
to understand their structure yet, but they'll give you an idea of
the kind of capabilities that the SQL language offers.
</p>
<ul>
<li> Which students are studying for a CS degree (3778)?
<pre>
select * from Students where degree=3778;
</pre>
<p>
<li> How many students are studying for a CS degree?
<pre>
select count(*) from Students where degree=3778;
</pre>
<p>
<li> Who are the professors?
<pre>
select * from Staff where position ilike '%professor%';
</pre>
<p>
<li> How many students are enrolled in each course?
<pre>
select course,count(*) from Enrolment group by course;
</pre>
<p>
<li> Which courses is Andrew Taylor teaching?
<pre>
select c.code, c.title
from   Courses c, Staff s
where  s.name='Andrew Taylor' and c.lecturer=s.userid;
</pre>
<p>
<p>or</p>
<pre>
select c.code, c.title
from   Courses c join Staff s on (c.lecturer=s.userid)
where  s.name='Andrew Taylor';
</ul>
<p>
The last query is laid out as we normally lay out more complex
SQL statements: with a keyword starting each line, and each
clause of the SQL statement starting on a separate line.
</p>
<p>
Try experimenting with variations of the above queries.
</p>
</div>
<p>


<h4>Sorting out Problems</h4>

<p>
It is very difficult to diagnose problems with software over email,
unless you give sufficient details about the problem.
An email that's as vague as <q>My PostgreSQL server isn't
working. What should I do?</q>, is basically useless.
Any email about problems with software should contain details of
</p>
<ul>
<li> what you were attempting to do
<li> precisely what commands you used
<li> exactly what output you got 
</ul>
<p>
One way to achieve this is to copy-and-paste the last few commands
and responses into your email.
</p>
<p>
Alternatively, you should come to a consultation where we can work
through the problem via screen sharing (which is usually very quick).
</p>

<h4>Can't shut server down?</h4>
<p>
When you use <code>p0</code> to shut down your PostgreSQL server,
you'll observe something like:
</p>
<pre>
$ <b>p0</b>
waiting for server to shut down....
</pre>
<p>
<p>
Dots will keep coming until the server is finally shut down, at which
point you will see:
</p>
<pre>
$ <b>p0</b>
waiting for server to shut down........ done
server stopped
</pre>
<p>
<p>
Sometimes, you'll end up waiting for a long time and the server still
doesn't shut down. This is typically because you have an <code>psql</code>
session running in some other window (the PostgreSQL server won't shut
down until all clients have disconnected from the server).
The way to fix this is to find the <code>psql</code> session and end it.
If you can find the window where it's running, simply use <code>\q</code>
to quit from <code>psql</code>.
If you can't find the window, or it's running from a different machine
(e.g. you're in the lab and find that you left a <code>psql</code> running
at home), then use <code>ps</code> to find the process id of the
<code>psql</code> session and stop it using the Linux <code>kill</code>
command.
</p>

<h4>Can't restart server?</h4>
<p>
Occasionally, you'll find that 
your PostgreSQL server was not shut down cleanly the last time you
used it and you cannot re-start it next time you try to use it.
We'll discuss how to solve that here ...
</p>
<p>
The typical symptoms of this problem are that you log in to
<code>vxdb02</code>, set up your environment, try to start
your PostgreSQL server and you get the message:
</p>
<pre>
pg_ctl: another server may be running; trying to start server anyway
waiting for server to start.... stopped waiting
pg_ctl: could not start server
Examine the log output.
</pre>
<p>
<p>
When you go and check the log file, you'll probably find,
right at the end, something like:
</p>
<pre>
$ <b>tail -2 $PGDATA/Log</b>
FATAL:  lock file "postmaster.pid" already exists
HINT:  Is another postmaster (PID <i>NNNN</i>) running in data directory "/localstorage/$USER/pgsql"?
</pre>
<p>
<p>
where <code><i>NNNN</i></code> is a number.
</p>
<p>
There are two possible causes for this: the server is already running,
or the server did not terminate properly after the last time you used it.
You can check whether the server is currently running by the command
<code>psql -l</code>. If that gives you a list of your databases, then
you simply forgot to shut the server down last time you used it and it's
ready for you to use again. If <code>psql -l</code> tells you that
there's no server running, then you'll need to do some cleaning up
before you can restart the server ...
</p>
<p>
When the PostgreSQL server is run, it keeps a record of the Unix process
that it's running as in a file called:
</p>
<pre>
$PGDATA/postmaster.pid
</pre>
<p>
<p>
Normally when your PostgreSQL server process terminates (e.g. via
<code>p0</code>), this file will be removed. If your PostgreSQL
server stops, and this file persists, then <code>p1</code> becomes
confused and thinks that there is still a PostgreSQL server running
even though there isn't.
</p>
The first step in cleaning up is to remove this file:
<pre>
$ <b>rm $PGDATA/postmaster.pid</b>
</pre>
<p>
<p>
You should also clean up the socket files used by the PostgreSQL
server. You can do this via the command:
</p>
<pre>
$ <b>rm $PGDATA/.s*</b>
</pre>
<p>
<p>
Once you've cleaned all of this up, then the <code>p1</code>
command ought to allow you to start your PostgreSQL server ok.
</p>
<br>
<p>
Happy PostgreSQL'ing, <i>jas</i>
</p>

<br>
<br>
<h4>Detailed description of Steps 2 and 3 of Setting Up</h4>
<p>
<h4>Stage 2: Setting up your environment (The long way)</h4>
<p>
<b>If you have used <code>3311 pginit</code> you do not need to complete this step</b>
</p>
<p>
PostgreSQL needs certain configuration settings to be just right
bfore it will work.
Part of setting up this environment involves setting some
shell environment variables.
</p>
<p>
The following commands will set up the environment appropriately:
</p>
<pre>
I=/localstorage/cs3311/postgresql/17
PGDATA=/localstorage/$USER/pgsql/data
PGHOST=$PGDATA
LD_LIBRARY_PATH=$I/lib
PATH=$I/bin:$PATH
export PGDATA PGHOST LD_LIBRARY_PATH PATH

alias p0="$I/bin/pg_ctl stop"
alias p1="$I/bin/pg_ctl -l $PGDATA/log start"
</pre>
<p>
<p>
The critical environment variables here are <code>PGDATA</code>
(which indicates where all of the PostgreSQL data files are located),
and <code>PGHOST</code>
(which tells where the sockets are located that you use to connect
to the PostgreSQL server).
Of course, all of the other settings are important as well.
</p>
<p>
A useful place for these commands is in a file called
</p>
<pre>
/localstorage/$USER/env
</pre>
<p>
<p>
You should create this file and copy the above commands
into it.
</p>
<p>
Once you've done this, running the command
</p>
<pre>
$ <b>source /localstorage/$USER/env</b>
</pre>
<p>
<p>
will set the environment appropriately.
</p>
<p>
You will need to set your environment each time you login to
<code>vxdb02</code> for a session with PostgreSQL.
</p>
<p>
If you know how to write shell scripts, you could modify
your <code>.bashrc</code> so that it <code>source</code>'d
the <code>env</code> script automatically, each time you
login to <code>vxdb02</code>.
</p>

<h4>Stage 3: Setting up your PostgreSQL Server (The long way)</h4>
<p>
<b>If you have used <code>3311 pginit</code> you do not need to complete this step</b>
</p>
<p>
A necessary first step to installing a PostgreSQL server
is to set up your environment as described in Stage 2.
Once you've done that, you can create the directories
and files to manage your databases.
</p>
<p>
The <code>initdb</code> command creates these directories
and places some configuration files in them.
You only need to run <code>initdb</code>  once (unless you need to
completely reinstall your PostgreSQL server from scratch).
</p>
<p>
If you used the environment setting described above,
<code>initdb</code> will create your PostgreSQL server
setup in the directory:
</p>
<pre>
/localstorage/$USER/pgsql
</pre>
<p>
<p>
An example of using <code>initdb</code>
</p>
<pre>
$ <b>which initdb</b>
/localstorage/cs3311/postgresql/17/bin/initdb
$ <b>initdb</b>
The files belonging to this database system will be owned by user "<span class="red">jas</span>".
This user must also own the server process.

The database cluster will be initialized with locale "en_AU.UTF-8".
The default database encoding has accordingly been set to "UTF8".
The default text search configuration will be set to "english".

Data page checksums are disabled.

creating directory /localstorage/<span class="red">jas</span>/pgsql/data ... ok
creating subdirectories ... ok
selecting dynamic shared memory implementation ... posix
selecting default max_connections ... 100
selecting default shared_buffers ... 128MB
selecting default time zone ... Australia/Sydney
creating configuration files ... ok
running bootstrap script ... ok
performing post-bootstrap initialization ... ok
syncing data to disk ... ok

initdb: warning: enabling "trust" authentication for local connections
You can change this by editing pg_hba.conf or using the option -A, or
--auth-local and --auth-host, the next time you run initdb.

Success. You can now start the database server using:

    pg_ctl -D /localstorage/jas/pgsql/data -l logfile start
</pre>
<p>
<p>
Naturally, the occurrences of <span class="red"><tt>jas</tt></span>
will be replaced by your zID.
</p>
<p>
Also, don't get too excited and run the above <code>pg_ctl</code>
command just yet. There is a bit more setup to do ...
</p>
<pre>
$ <b>cd /localstorage/$USER/pgsql/data</b>
$ <b><i>edit</i> postgresql.conf</b>
</pre>
<p>
<p>
where you replace the word <code><b><i>edit</i></b></code>
by your favourite text editor.
</p>
<p>
The <code>postgresql.conf</code> file is the main configuration
file for your PostgreSQL server.
You need to modify it so that it works with the environment you
set up above.
This requires changes to the lines highlighted in red in this
excerpt from <code>postgresql.conf</code>
</p>
<pre>
...
# - Connection Settings -

<span class="red">#listen_addresses = 'localhost'     # what IP address(es) to listen on;</span>
                    # comma-separated list of addresses;
                    # defaults to 'localhost'; use '*' for all
                    # (change requires restart)
#port = 5432                # (change requires restart)
max_connections = 100           # (change requires restart)
#superuser_reserved_connections = 3 # (change requires restart)
<span class="red">#unix_socket_directories = '/var/run/postgresql'    # comma-separated list of directories</span>
                    # (change requires restart)
...
</pre>
<p>
<p>
This should be changed to
</p>
<pre>
...
# - Connection Settings -

<span class="green">listen_addresses = ''     # what IP address(es) to listen on;</span>
                    # comma-separated list of addresses;
                    # defaults to 'localhost'; use '*' for all
                    # (change requires restart)
#port = 5432                # (change requires restart)
max_connections = 100           # (change requires restart)
#superuser_reserved_connections = 3 # (change requires restart)
<span class="green">unix_socket_directories = '<i>DirectoryNameMatchingPGHOST</i>'    # comma-separated list of directories</span>
                    # (change requires restart)
...
</pre>
<p>
<p>
<span class="red"><b>Very important notice:</b></span>
when we changed those two lines, we also
removed the <code>#</code> from the start of the line. If you don't
remove this, the line remains commented out and your changes will have
no effect.
</p>
<p>
The string on the <code>unix_socket_directories</code> line must
contain the same directory name as you get via the shell command:
</p>
<pre>
$ <b>echo $PGHOST</b>
</pre>
<p>
<p>
You can also change the value of <code>max_connections</code> to
something smaller than 100 (e.g. 10). This is not necessary, but
I do this to make PostgreSQL use slightly less runtime memory.
</p>
<p>
After making these changes, your PostgreSQL server is ready to go.
</p>
<div class="note">
<p>
One place where PostgreSQL is less space efficient than it might be
is in the size of its transaction logs. These logs live in the directory
<code>pgsql/data/pg_wal</code> and are essential for the functioning of
your PostgreSQL server. If you remove any files from this directory,
you will render your server inoperable. Similarly, manually changing
the files under <code>pgsql/data/base</code> and its subdirectories will
probably break your PostgreSQL server.
</p>
<p>
If you mess up your PostgreSQL server badly enough, it will need to
be re-installed. If such a thing happens, all of your databases are
useless and all of the data in them is irretrievable. You will need
to completely remove the
<code>/localstorage/$USER/pgsql</code> directory and re-install as
above.
You should only do this in extreme circumstances; most people will
install the server directories/files once, and they will use the
same installation until the end of the term.
</p>
<p>
If you need to remove the <code>pgsql</code> directory, then all of
your databases and any data in them are gone forever.
This is not a problem if you set up your databases by loading new
views, functions, data, etc. from a file, but if you type <code>create</code>
commands directly into the database, then the created objects will be
lost.
The best way to avoid such catastrophic loss of data is to type your
SQL <code>create</code> statements into a file and load them into the
database from there. Alternatively, you'd need to do regular
back-ups of your databases using the <code>pg_dump</code> command.
</p>
</div>
<p>
