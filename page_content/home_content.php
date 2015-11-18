<div class="post highlighted">
<h2>Welcome</h2>
<p>Welcome to Caspar's Theater. This is the home of good productions. We have every kind of show you could care to watch. Here is the place to be. The place to be is here. The place to be is not there, it's here. Here. From this website you can browse our productions, present, future and now! There is also a link to see which shows will be playing soon, in the near future, tonight and tomorrow and in the far future, as well as all in between now and then, and even more importantly, then and now! If you wish to book a show, you can choose which show to book first by going to the productions, or to the shows, or to the booking place, where you will be presented with a list, a huge list, a list of all, yes all, of our shows. Can you believe it, yes all of them are available for booking! Except of course the ones that are fully booked. Of course!</p>

<p>Below is a list of our most popular productions. Check back soon for more!</p>
</div>

<?php

$zones = "SELECT * from Zone;";
$results = $conn->query($zones);
foreach ($results as $row) {
	echo $row["name"]." ".$row["price_multiplier"]."<br>";
}
$productions = "SELECT * from Production;";
$results = $conn->query($productions);
foreach ($results as $row) {
	echo $row["title"]."<br>";
}
$shows = "SELECT * from Performance";
$results = $conn->query($shows);
foreach ($results as $row) {
	echo $row["date_time"]."<br>";
}

?>

<!-- Here will go a list of the most popular productions-->