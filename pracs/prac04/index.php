<?php
require("../../course.php"); $exID = "04";
$exWeb = HOME."/pracs/$exID"; $exDir = HOMEDIR."/pracs/$exID";
echo startPage("Prac Exercise $exID","","SQL Queries, Views, and Aggregates (ii)");
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
<a href="https://www.postgresql.org/docs/current/static/index.html">PostgreSQL Manual</a>,
<a href="http://www.sqlite.org/docs.html">SQLite Manual</a>.
You should become familiar with where to find useful information in
the documentation ASAP; you will need to know how to use PostgreSQL for
the assignments and how to use SQLite for the exam.

<h3>Background</h3>
<p>
In lectures, we used a simple database about beers, bars and drinkers to
illustrate aspects of querying in SQL.
The database was designed to simplify queries by using symbolic primary
keys (e.g., <tt>Beers.name</tt>).
In practice, we don't use symbolic primary keys because: (a) they typically
occupy more space than a numeric key, (b) symbolic names have an annoying
tendency to change over time (e.g., you might change your email, and
having email as a primary key creates a multitude of update problems).
Therefore, we have designed a (slightly) more realistic schema for representing
the same information:
</p>
<center>
<img src="Pic/beer-v2-schema.png">
</center>
<p>
The SQL schema will obviously be different to the schema used in lectures,
and is available in the file:
</p>
<pre>
<?=$exDir?>/schema.sql
</pre>
<p>
This schema is written in portable SQL and so should load into both
PostgreSQL and SQLite without errors.
</p>
<p>
If you're working on your laptop (and not via <tt>putty</tt>), you can
grab copies of all files used in this Prac in the ZIP archive:
</p>
<pre>
<a href="<?=$exWeb?>/prac.zip"><?=$exDir?>/prac.zip</a>
</pre>

<h3> Setting up the PostgreSQL Database</h3>
<p>
Login to a machine with a PostgreSQL server running.
If you already have a <tt>beer2</tt> database and you want to replace it,
you will, of course, need to drop it first:
</p>
<pre>
$ <b>dropdb beer2</b>
</pre>
<p>
Then do the following:
</p>
<pre>
$ <b>createdb beer2</b>
<comment>... make a new empty database ...</comment>
$ <b>psql beer2 -f <?=$exDir?>/schema.sql</b>
<comment>... load the schema ... produces CREATE TABLE, etc. messages ...</comment>
$ <b>psql beer2 -f <?=$exDir?>/data.sql</b>
<comment>... load the tuples ... poduces INSERT messages ...</comment>
</pre>
<p>
You now have a database that you can use via:
</p>
<pre>
$ <b>psql beer2</b>
<comment>... run SQL commands ...</comment>
</pre>

<h3>Setting up the SQLite Database</h3>
<p>
Login to a machine with SQLite installed, change to the directory
containing the <tt>schema.sql</tt> and <tt>data.sql</tt> files
mentioned above, and do the following:
</p>
<pre>
% <b>sqlite3 beer2.db</b>
SQLite version 3.8.7.1 2014-10-29 13:59:56
Enter ".help" for usage hints.
sqlite&gt; <b>.read schema.sql</b>
sqlite&gt; <b>.read data.sql</b>
sqlite&gt; <b>.quit</b>
% 
</pre>
<p>
This will create a file called <tt>beer2.db</tt> in the current directory.
You now have a database that you can use via:
</p>
<pre>
$ <b>sqlite3 beer2.db</b>
<comment>... run SQL commands ...</comment>
</pre>

<h3>Exercises</h3>
<p>
Use the two databases you created above to do the exercises below.
The same view definitions should work in both databases.
Perhaps you could alternate between developing and testing the
view first in PostgreSQL and then testing it in SQLite, and vice
versa.
The aim is to get practice in building queries in both databases.
</p>
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
file (<a href="queries.sql"><tt>queries.sql</tt></a>) is available.
While you're developing your views, you might find it convenient
to edit the views in one window (i.e. edit the <tt>queries.sql</tt>
file containing the views) and copy-and-paste the view definitions
into another window running <tt>psql</tt> or <tt>sqlite3</tt>.
</p>
<p>
Note that the order of tuples in the results does not matter.
As long as you have the same set of tuples, your view is correct.
Remember that, in theory, the output from an SQL query is a set.
Some test queries use an explicit ordering, but that should not
be included in the view definition.
</p>
<p>
Note also that the sample outputs typically use column names
that are different to the column names in the table.
You should use the column names given in the sample output;
treat them as part of description of the question.
</p>
<p>
Once you have completed each of the view definitions, you can
test it simply by typing:
</p>
<pre>
beer2=# <b>select * from Q<i>n</i>;</b>
</pre>
<p>
and observing whether the result matches the expected result
given below.
Note that I'll give all the results in PostgreSQL format;
the SQLite tuples don't look precisely the same, but it
should be clear enough that it <i>is</i> the same set of
tuples.
</p>

<h3>Queries on Beer Database v2</h3>
<p>
Write an SQL view to answer each of the following queries.
Note that <em>none</em> of your queries should contain internal <tt>id</tt>
values; all references to entities in queries should be via their name.
</p>
<ol>
<li>
<p>
<b><i>What beers are made by Toohey's?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q1;</b>
    beer     
-------------
 New
 Old
 Red
 Sheaf Stout
(4 rows)
</pre>
</li>
<li>
<p>
<b><i>Show beers with headings "Beer", "Brewer".</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q2;</b>
           Beer           |       Brewer        
--------------------------+---------------------
 80/-                     | Caledonian
 Amber Ale                | James Squire
 Bigfoot Barley Wine      | Sierra Nevada
 Burragorang Bock         | George IV Inn
 Chestnut Lager           | Bridge Road Brewers
 Crown Lager              | Carlton
 Fosters Lager            | Carlton
 India Pale Ale           | James Squire
 Invalid Stout            | Carlton
 Melbourne Bitter         | Carlton
 New                      | Toohey's
 Nirvana Pale Ale         | Murray's
 Old                      | Toohey's
 Old Admiral              | Lord Nelson
 Pale Ale                 | Sierra Nevada
 Pilsener                 | James Squire
 Porter                   | James Squire
 Premium Lager            | Cascade
 Red                      | Toohey's
 Sink the Bismarck        | Brew Dog
 Sheaf Stout              | Toohey's
 Sparkling Ale            | Cooper's
 Stout                    | Cooper's
 Tactical Nuclear Penguin | Brew Dog
 Three Sheets             | Lord Nelson
 Victoria Bitter          | Carlton
(26 rows)
</pre>
</li>
<li>
<p>
<b><i>Find the brewers whose beers John likes.</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q3;</b>
    brewer     
---------------
 Brew Dog
 James Squire
 Lord Nelson
 Sierra Nevada
 Caledonian
(5 rows)
</pre>
</li>
<li>
<p>
<b><i>How many different beers are there?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q4;</b>
 #beers 
--------
     26
(1 row)
</pre>
</li>
<li>
<p>
<b><i>How many different brewers are there?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q5;</b>
 #brewers 
----------
       12
(1 row)
</pre>
</li>
<li>
<p>
<b><i>Find pairs of beers by the same manufacturer</i></b> (but no (a,b) and (b,a) pairs, or (a,a))
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q6;</b>
        beer1        |          beer2           
---------------------+--------------------------
 Amber Ale           | Porter
 Amber Ale           | Pilsener
 Amber Ale           | India Pale Ale
 Bigfoot Barley Wine | Pale Ale
 Crown Lager         | Victoria Bitter
 Crown Lager         | Melbourne Bitter
 Crown Lager         | Invalid Stout
 Crown Lager         | Fosters Lager
 Fosters Lager       | Victoria Bitter
 Fosters Lager       | Melbourne Bitter
 Fosters Lager       | Invalid Stout
 India Pale Ale      | Porter
 India Pale Ale      | Pilsener
 Invalid Stout       | Victoria Bitter
 Invalid Stout       | Melbourne Bitter
 Melbourne Bitter    | Victoria Bitter
 New                 | Sheaf Stout
 New                 | Red
 New                 | Old
 Old                 | Sheaf Stout
 Old                 | Red
 Old Admiral         | Three Sheets
 Pilsener            | Porter
 Red                 | Sheaf Stout
 Sink the Bismarck   | Tactical Nuclear Penguin
 Sparkling Ale       | Stout
(26 rows)
</pre>
</li>
<li>
<p>
<b><i>How many beers does each brewer make?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q7 order by brewer;</b>
       brewer        | nbeers 
---------------------+--------
 Brew Dog            |      2
 Bridge Road Brewers |      1
 Caledonian          |      1
 Carlton             |      5
 Cascade             |      1
 Cooper's            |      2
 George IV Inn       |      1
 James Squire        |      4
 Lord Nelson         |      2
 Murray's            |      1
 Sierra Nevada       |      2
 Toohey's            |      4
(12 rows)
</pre>
</li>
<li>
<p>
<b><i>Which brewer makes the most beers?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q8;</b>
 brewer  
---------
 Carlton
(1 row)
</pre>
</li>
<li>
<p>
<b><i>Beers that are the only one by their brewer.</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q9;</b>
       beer       
------------------
 80/-
 Burragorang Bock
 Chestnut Lager
 Nirvana Pale Ale
 Premium Lager
(5 rows)
</pre>
</li>
<li>
<p>
<b><i>Beers sold at bars where John drinks.</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q10 order by beer;</b>
       beer        
-------------------
 Burragorang Bock
 New
 Old
 Old Admiral
 Pale Ale
 Sink the Bismarck
 Sparkling Ale
 Three Sheets
 Victoria Bitter
(9 rows)
</pre>
<p>
You might like to consider a variation on this query to find
just the beers that John likes that are sold in the bars where
he drinks. The solution is given in the solutions file.
</p>
</li>
<li>
<p>
<b><i>Bars where either Gernot or John drink.</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q11 order by bar;</b>
      bar 
------------------
 Australia Hotel
 Coogee Bay Hotel
 Local Taphouse
 Lord Nelson
 Royal Hotel
(5 rows)
</pre>
</li>
<li>
<p>
<b><i>Bars where both Gernot and John drink.</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q12;</b>
     bar     
-------------
 Lord Nelson
(1 row)
</pre>
</li>
<li>
<p>
<b><i>Bars where John drinks but Gernot doesn't</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q13;</b>
       bar        
------------------
 Australia Hotel
 Local Taphouse
 Coogee Bay Hotel
(3 rows)
</pre>
</li>
<li>
<p>
<b><i>What is the most expensive beer?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q14;</b>
       beer        
-------------------
 Sink the Bismarck
(1 row)
</pre>
</li>
<li>
<p>
<b><i>Find bars that serve New at the same price as the Coogee Bay Hotel charges for VB.</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q15;</b>
     bar     
-------------
 Royal Hotel
(1 row)
</pre>
</li>
<li>
<p>
<b><i>Find the average price of common beers</i></b> (where "common" = served in more than two hotels)
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q16;</b>
      beer       | AvgPrice 
-----------------+----------
 Victoria Bitter |     2.40
 New             |     2.59
 Old             |     2.68
(3 rows)
</pre>
</li>
<li>
<p>
<b><i>Which bar sells 'New' cheapest?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q17;</b>
     bar      
--------------
 Regent Hotel
(1 row)
</pre>
</li>
<li>
<p>
<b><i>Which bar is most popular?</i></b> (Most drinkers)
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q18;</b>
       bar        
------------------
 Coogee Bay Hotel
 Lord Nelson
(2 rows)
</pre>
</li>
<li>
<p>
<b><i>Which bar is least popular?</i></b> (May have no drinkers)
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q19;</b>
       bar       
-----------------
 Local Taphouse
 Marble Bar
 Regent Hotel
 Australia Hotel
 Royal Hotel
(5 rows)
</pre>
</li>
<li>
<p>
<b><i>Which bar is most expensive?</i></b> (Highest average price)
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q20;</b>
      bar       
----------------
 Local Taphouse
(1 row)
</pre>
</li>
<li>
<p>
<b><i>Which beers are sold at all bars?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q21;</b>
 beer 
------
(0 rows)
</pre>
<p>
i.e. no beers are sold at all bars.
</p>
</li>
<li>
<p>
<b><i>Price of cheapest beer at each bar?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q22;</b>
       bar        | min_price 
------------------+-----------
 Coogee Bay Hotel |      2.25
 Local Taphouse   |      7.50
 Royal Hotel      |      2.30
 Australia Hotel  |      3.00
 Regent Hotel     |      2.20
 Marble Bar       |      2.80
 Lord Nelson      |      3.00
(7 rows)
</pre>
</li>
<li>
<p>
<b><i>Name of cheapest beer at each bar?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q23;</b>
       bar        |      beer       
------------------+-----------------
 Australia Hotel  | New
 Coogee Bay Hotel | New
 Lord Nelson      | New
 Marble Bar       | New
 Marble Bar       | Victoria Bitter
 Regent Hotel     | New
 Regent Hotel     | Victoria Bitter
 Royal Hotel      | Victoria Bitter
 Royal Hotel      | New
 Local Taphouse   | Pale Ale
(10 rows)
</pre>
</li>
<li>
<p>
<b><i>How many drinkers are in each suburb?</i></b>
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q24;</b>
   addr   | count 
----------+-------
 Randwick |     1
 Mosman   |     1
 Newtown  |     1
 Clovelly |     1
(4 rows)
</pre>
</li>
<li>
<p>
<b><i>How many bars in suburbs where drinkers live?</i></b> (must include suburbs with no bars)
</p>
<p>
In PostgreSQL, the results should look like:
</p>
<pre>
beer2=# <b>select * from Q25;</b>
   addr   | #bars 
----------+-------
 Randwick |     1
 Mosman   |     0
 Newtown  |     0
 Clovelly |     0
(4 rows)
</pre>
</li>


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
Make a copy of your <tt>queries.sql</tt> file and start testing
the views that you created for PostgreSQL for SQLite.
If you want to make the query results from SQLite look more like
those from PostgreSQL, run the following commands at the start
of your SQLite session:
</p>
<pre>
$ <b>sqlite3 beer2.db</b>
SQLite version 3.8.7.1 2014-10-29 13:59:56
Enter ".help" for usage hints.
sqlite> <b>.headers on</b>
sqlite> <b>.mode column</b>
sqlite> <b>.width 20 20 20 20</b>
sqlite> <comment>... continue with your SQL queries ... </comment>
</pre>
<p>
Note that SQLite is not quite as smart as PostgreSQL when it comes to choosing
column widths. All columns will be 20 characters wide if you use the above
settings. Note that any values that are wider than 20 characters will be truncated.
If you can't be bothered typing these commands each time, put them in a file
called <tt>.sqliterc</tt> in your home directory and they'll be executed each
time you run <tt>sqlite3</tt>.
</p>
<p>
As we noticed in the <a href="../04/index.php">previous Prac Exercise</a>,
SQLite doesn't support the definition of views via:
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
Note that this approach would cause problems in PostgreSQL, which records
which views depend on which other views.
Inevitably, you would try to drop a view defined early in the SQL file
that is used by view later in the file.
In PostgreSQL, you can add the keyword <tt>cascade</tt> to ensure that
you not only drop the view you asked to drop, but also all the views that
make use of it.
SQLite doesn't do this dependency checking, and will allow you to drop
a view, even if other views use it. You won't notice until you try to use
one of the remaining views and will then be told that the view you dropped
is undefined. If you redefine the view straight away (as in the above),
then the dependency problem never arises.
</p>
<p>
If you're a <tt>vim</tt> user, the following command will convert all of
the current <tt>create view</tt> statements into an appropriate form.
If you're not a <tt>vim</tt> user, read and weep ... or post the equivalent
for your editor of choice.
</p>
<pre>
:s/^create or replace view \([^ ]*\)/drop view if exists \1;<span style="color:#0000DD">^M</span>create view \1/
</pre>
<p>
Note: the <span style="color:#0000DD"><tt>^M</tt></span> is achieved
by typing the two-character sequence control-V control-M.
Also, if you attempt this in <tt>vim</tt> and mess it up, you can undo the
effects simply by typing the character <tt>u</tt>.
</p>
<p>
To avoid the fact that SQLite doesn't support view definitions of the form:
</p>
<pre>
create view <i>ViewName</i>(<i>attr<sub>1</sub></i>, <i>attr<sub>2</sub></i>, ...)
as
select <i>expr<sub>1</sub></i>, <i>expr<sub>2</sub></i>, ...
</pre>
<p>
simply use the equivalent form:
</p>
<pre>
create view <i>ViewName</i>
as
select <i>expr<sub>1</sub></i> as <i>attr<sub>1</sub></i>, <i>expr<sub>2</sub></i> as <i>attr<sub>2</sub></i>, ...
</pre>
<p>
Once you've made the above changes, many of the views will work
correctly in both PostgreSQL and SQLite.
</p>
<p>
One "problem" is that SQLite doesn't have quite the same formatting
options for result values.
PostgreSQL allows you to cast to a <tt>numeric</tt> value to control
the number of decimal places in real number values; unfrotunately,
SQLite doesn't quite allow the same. Don't worry if your real results
don't look the same in SQLite.
</p>
You should attempt the above exercises before looking at the
<a href="soln2.sql">SQLite sample solutions</a>.
</p>
</body>
</html>
