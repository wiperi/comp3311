<?php
require("../../course.php"); $exID = "07";
$exWeb = HOME."/pracs/$exID"; $exDir = HOMEDIR."/pracs/$exID";
define("PGDOCS","http://postgresql.org/docs/13");
echo startPage("Prac Exercise $exID","","Python/Pscopg2 and a Beer Database");
echo updateBlurb();
?>

<h3>Aims</h3>

<p>
This exercise aims to give you practice in:
</p>
<ul>
<li> writing Python code to access a PostgreSQL database
<li> building a Python script that can run as a command
</ul>
<p>
This exercise will not explain how to do everything in fine detail.
Part of the aim of the exercise is that you explore how to use the
Python, Psycopg2 and PostgreSQL systems.
The documentation for the versions of these systems on <tt>d2</tt>:
<a href="https://docs.python.org/release/3.9.2/">Python3</a>,
<a href="https://www.psycopg.org/docs/">Psycopg2</a>,
<a href="https://www.postgresql.org/docs/13/index.html">PostgreSQL</a>.
</p>

<h3>Background</h3>

<p>
In this exercise we are using the same database as for
<a href="../06/index.php">Prac 06</a>:
an on-line beer rating system which describes beers, breweries,
tasters, ratings, etc.
</p>
<p>
A relational schema for this database is available in the file
</p>
<pre>
<a href="<?="$exWeb/schema.sql"?>"><?="$exDir/schema.sql"?></a>
</pre>
<p>
A dump file containing both the schema and the data for this database
is available in the file
<pre>
<a href="<?="$exWeb/beers.dump"?>"><?="$exDir/beers.dump"?></a>
</pre>
<p>
The relational model attempts to capture all of the semantics of the
E/R design.
However, there is one difference between the relational model and the
E/R design in that beer styles have been converted into entities. This
is primarily to ensure that all beer style information looks consistent
(e.g. we don't have some beers called <q>lager</q> and others called
<q>laager</q>).
</p>
<p>
You should create a new database called <q>beers</q> and load the schema
and data into this database. The following commands wil do this:
</p>
<pre>
$ <b>createdb beers</b>
CREATE DATABASE
$ <b>psql beers -f <?="$exDir/beers.dump"?></b>
<i>... which will produce CREATE TABLE, COPY and ALTER TABLE messages ...</i>
</pre>
<p>
If you get any error messages from the above commands,
read them carefully, diagnose the problem, and fix it.
</p>
<p>
Once the schema and data are loaded, check that everything is in order
by running the following queries and seeing whether you get the same
results:
</p>
<pre>
beers=# <b>select count(*) from Ratings;</b>
 count 
-------
    32
(1 row)

beers=# <b>select given from Taster order by given;</b>
 given  
--------
 Adam
 Geoff
 Hector
 Jeff
 John
 Peter
 Raghu
 Ramez
 Rose
 Sarah
(10 rows)
</pre>
<p>
If the database doesn't look correct, try to work out what went wrong
and then try to load the data correctly.
Once you're statisfied that the database is correct, continue with
the exercises.
</p>

<h3>Exercises</h3>

<p>
Write a Python/Psycopg2 script that gives average ratings for three
different kinds of entity: tasters, beers and brewers.
The script has the following usage:
</p>
<pre>
Usage: ./avgrat taster|beer|brewer Name
</pre>
The command is called <tt>avgrat</tt> (short for "average ratings")
and takes two command line arguments: type of entity and a name.
The type of entity can be either <tt>taster</tt>, <tt>beer</tt> or
<tt>brewer</tt>. The behaviour of the script is as follows for each of these:
</p>
<ul>
<li> <tt>taster</tt> ... takes the given Name of a taster and shows the average rating they give for beers
<li> <tt>beer</tt> ... take the Name of a beer and shows the average rating for that beer
<li> <tt>brewer</tt> ... takes the Name of a brewer and shows the average rating for beers made by that brewer
</ul>
<p>
Note that the names have to match exactly the names in the database.
</p>
<p>
You will need to create script from scratch yourself.
There are examples of such scripts in the Data entries for the Week 7
<a href="https://webcms3.cse.unsw.edu.au/COMP3311/23T1/resources/82772">Lecture Material</a>.
Make sure that you add the appropriate <tt>#!</tt> line at the start
of the script, and that you make the script executable.
</p>
<p>
Some examples of how the script should work:
</p>
<pre>
$ <b>./avgrat not right</b>
Usage: ./avgrat taster|beer|brewer Name
$ <b>./avgrat taster John</b>
Average rating for taster John is 3.1
$ <b>./avgrat taster Sarah</b>
Average rating for taster Sarah is 2.7
$ <b>./avgrat taster sarah</b>
No taster called 'sarah'   <span class="comment"># doesn't exactly match upper/lower-case</span>
$ <b>./avgrat brewer Chimay </b>
Average rating for brewer Chimay is 3.0
$ <b>./avgrat brewer 'Sierra Nevada'</b>
Average rating for brewer Sierra Nevada is 3.8
$ <b>./avgrat brewer "Pete's"</b>
No ratings for Pete's
$ <b>./avgrat beer New</b>
Average rating for beer New is 1.5
$ <b>./avgrat beer 'Sierra Nevada Pale Ale'</b>
Average rating for beer Sierra Nevada Pale Ale is 4.0
</pre>
<p>
You should try to solve the problem yourself before
looking at the below.
</p>
<p>
A partial solution is available in the file
</p>
<pre>
<?="$exDir/avgrat"?>
</pre>
<p>
It is deficient in being unable to distinguish between
an X with no ratings and an X that does not exist in the database.
</p>
<?=endPage()?>
