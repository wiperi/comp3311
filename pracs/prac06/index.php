<?php
require("../../course.php"); $exID = "06";
$exWeb = HOME."/pracs/$exID"; $exDir = HOMEDIR."/pracs/$exID";
echo startPage("Prac Exercise $exID","","Updates and Triggers");
echo updateBlurb();
?>

<h3>Aims</h3>

<p>
This exercise aims to give you practice in:
</p>
<ul>
<li> implementing triggers via PLpgSQL functions
</ul>
<p>
The following sections of the <a href="https://www.postgresql.org/docs/current/static/index.html">PostgreSQL Documentation</a> will be useful for this lab:
<ul>
<li>the SQL 
<a href="https://www.postgresql.org/docs/current/static/sql-createtrigger.html"><code>CREATE TRIGGER</code></a>
statement;
</li>
<li>
the 
<a href="https://www.postgresql.org/docs/current/static/plpgsql.html">PLpgSQL</a>
language description.
</li>
</ul>
</p>

<h3>Background</h3>

<p>
For this Lab, we will make use of a similar beer ratings
database to the one we used in Prac Exercise 06.
Files containing a relational schema for this database and
data to populate the tables are available in the files:
</p>
<pre>
<a href="<?=$exWeb?>/schema.sql"><?=$exDir?>/schema.sql</a>
<a href="<?=$exWeb?>/data.sql"><?=$exDir?>/data.sql</a>
</pre>
<p>
You should save your old <q>beers</q> database (if you want to),
delete it, then re-create it afresh by
loading the schema and data into the new database.
The following commands will do this:
</p>
<pre>
$ <b>pg_dump beers > oldBeer.db</b>
$ <b>dropdb beers</b>
$ <b>createdb beers</b>
$ <b>psql beers -f <?=$exDir?>/schema.sql</b>
<i>... which will produce lots of NOTICE messages ...</i>
$ <b>psql beers -f <?=$exDir?>/data.sql</b>
<i>... which will produce messages about table ids ...</i>
</pre>
<p>
This will populate all of the tables except <code>Ratings</code>.
We will populate this later.
</p>
<p>
Imagine that the database used in Prac Exercise 06
ends up as the back-end database for a web site
<tt>MyWorldOfBeer.com</tt> with
thousands of beers, tens of thousands of raters and millions of
ratings.
</p>
<p>
One important function for such a site would be producing a list
of the top-ten rated beers. It would be possible to produce such
a list using the <code>BeerSummary</code> function from Prac
Exercise 06. However, as the database grows, this operation would
become slower and slower.
</p>
<p>
Let us imagine that with the size of database described above,
it is now intolerably slow to produce a list of
the top-ten rated beers.
The database designers decide to add three extra columns to the
<code>Beer</code> table to hold:
<ul>
<li> <code>totRating</code>: the sum of all ratings for each beer
<li> <code>nRatings</code>: the number of ratings for each beer
<li> <code>rating</code>: the average rating for each beer
	(<code>totRating/nRatings</code>)
</ul>
The values of these columns should always accurately reflect the state
of the <code>Ratings</code> table.
</p>
<p>
We can express what is required as a series of semi-formal assertions:
</p>
<pre>
for every Beer b (b.totRating = (sum(score) from Ratings where beer = b.id))
for every Beer b (b.nRatings = (count(score) from Ratings where beer = b.id))
for every Beer b (b.rating = b.totRating/b.nRatings, if b.nRatings > 0, null otherwise)
</pre>
<p>
Of course, ensuring that the database always satisfies these constraints
requires that the above columns in the <code>Beer</code> table be maintained
This, in turn, requires that some work is done every time a rating is
added, removed, or changed.

<h3>Exercise</h3>
<p>
Write triggers and their associated PLpgSQL functions to maintain
these assertions on the <code>Beer</code> table in response to all
possible changes to the <code>Ratings</code> table.
Place your trigger and function definitions in a file called 
<code>prac07.sql</code>.
</p>
<p>
You can assume that the only kind of update operation is one that
changes the rating by a given rater for a given beer.
In other words, the only updates will be of the form:
</p>
<pre>
update Ratings
set    score = X
where  rater = Y and beer = Z;
</pre>
<p>
If you want to loosen that assumption (i.e. allow absolutely any kind
of update), then that's fine too ... but it will require you write
extra code.
</p>
<p>
While you're developing your triggers, you should test them by adding
new rating records, updating existing rating records and deleting
rating records, and then checking whether the above assertions are
maintained.
</p>
<p>
For a final check, reset the database as above:
</p>
<pre>
$ <b>dropdb beers</b>
$ <b>createdb beers</b>
$ <b>psql beers -f <?=$exDir?>/schema.sql</b>
<i>... which will produce lots of NOTICE messages ...</i>
$ <b>psql beers -f <?=$exDir?>/data.sql</b>
<i>... which will produce messages about table ids ...</i>
$ <b>psql beers -f prac07.sql</b>
<i>... which will produce messages functions/triggers ...</i>
</pre>
<p>
and then run a sequence of modifications to the <code>Ratings</code>
table via:
</p>
<pre>
$ <b>psql beers -f <?=$exDir?>/test.sql</b>
</pre>
<p>
If you then check the contents of the <code>Beer</code> table, you
should observe:
</p>
<pre>
beers=# <b>select * from Beer order by id;</b>
 id |          name          | style | brewer | totrating | nratings | rating 
----+------------------------+-------+--------+-----------+----------+--------
  1 | Rasputin               |    10 |      9 |         8 |        3 |      2
  2 | 80/-                   |    13 |     11 |         4 |        1 |      4
  3 | Sierra Nevada Pale Ale |     3 |      6 |        20 |        5 |      4
  4 | Old Tire               |    11 |      7 |         5 |        1 |      5
  5 | Old                    |    12 |      3 |         7 |        2 |      3
  6 | New                    |     1 |      3 |         3 |        2 |      1
  7 | Fosters                |     1 |      1 |         3 |        1 |      3
  8 | James Squire Amber Ale |    12 |     12 |         3 |        1 |      3
  9 | James Squire Pilsener  |     2 |     12 |         7 |        2 |      3
 10 | Burragorang Bock       |     5 |      4 |         7 |        2 |      3
 11 | Scharer's Lager        |     1 |      4 |         3 |        1 |      3
 12 | Chimay Red             |     9 |     10 |         3 |        1 |      3
 13 | Chimay Blue            |     9 |     10 |         0 |        0 |       
 14 | Victoria Bitter        |     1 |      1 |         3 |        3 |      1
 15 | Sterling               |     1 |      1 |         0 |        0 |       
 16 | Empire                 |     1 |      1 |         6 |        2 |      3
 17 | Premium Light          |     1 |     14 |         0 |        0 |       
 18 | Sparkling Ale          |    12 |     13 |         0 |        0 |       
 19 | Sheaf Stout            |     3 |      3 |         0 |        0 |       
 20 | Crown Lager            |     1 |      1 |         2 |        1 |      2
 21 | Bigfoot Barley Wine    |     4 |      6 |         3 |        1 |      3
 22 | James Squire Porter    |     7 |     12 |         0 |        0 |       
 23 | Redback                |    14 |      5 |         9 |        2 |      4
 24 | XXXX                   |     1 |      2 |         5 |        1 |      5
 25 | Red                    |     1 |      3 |         0 |        0 |       
(25 rows)
</pre>
<p>
You will, of course, have observed that <tt>test.sql</tt> only performs
insert operations. We assume that you have tested the triggers for all
other operations yourself.
</p>
<p>
<a href="soln.sql">[Sample Solution]</a>
</p>

<?=endPage()?>
