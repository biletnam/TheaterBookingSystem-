<?php
//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.
$debug = TRUE;
$recreate_database_from_new = ($debug && isset($_GET["new_db"]) && $_GET["new_db"]=="YeS") ? TRUE : FALSE;

include('database/connect_to_database.php'); //connection closed at the end of this file

//SET PAGE VARIABLES
$page_title = "Caspar's Theater::Home Page";

$page_description = "Home Page of Caspar's Theatre, come here to see the best shows in town. Online Booking Available NOW!";

$current_page = "home";

//DO PAGE QUERIES
$newest_performances_sql = "
SELECT DISTINCT pr.* 
FROM Production pr
  JOIN Performance pe on pe.title = pr.title
ORDER BY pe.date_time DESC
LIMIT 10;
"; //todo get it to find the next performance
//todo get it to find out when it's first performance was/is

$newest_performances = $conn->query($newest_performances_sql);


//START THE TEMPALTE
include('template.php');

echo template_top($page_title, $page_description, menu($menu_items, $current_page));

//NOW FOR THE CONTENT
?>


<div class="post highlighted">
<img src="images/logo.png" width="280" align="left">
<h2>Welcome</h2>
<p>Welcome to Caspar's Theater. This is the home of good productions. We have every kind of show you could care to watch. Here is the place to be. The place to be is here. The place to be is not there, it's here. Here. From this website you can browse our productions, present, future and now! There is also a link to see which shows will be playing soon, in the near future, tonight and tomorrow and in the far future, as well as all in between now and then, and even more importantly, then and now! If you wish to book a show, you can choose which show to book first by going to the productions, or to the shows, or to the booking place, where you will be presented with a list, a huge list, a list of all, yes all, of our shows. Can you believe it, yes all of them are available for booking! Except of course the ones that are fully booked. Of course!</p>

<p>Below is a list of our most recent productions. Check back soon for more!</p>
</div>

<?php

function display_performance($performance){
	$title = $performance['title'];
	$description = $performance['description'];
	$mins = $performance['mins'];
	$genre = $performance['genre'];

	$cover_image_src = "images/$title/cover.jpg";
	$coverimage = "no cover image";
	//if (file_exists($coverimage)){
		$coverimage = "<img src=\"$cover_image_src\" height=\"300\" align=\"right\">";
	//}
	echo "<div class=\"post\">
		$coverimage
		<h2><a href=\"productions.php?production=$title\">$title</a></h2>
		<p>$description</p>
		<ul class=\"performance-details\">
			<li>Runtime: $mins minutes</li>
			<li>Genre: $genre</li>
		</ul>
	</div>";

}

foreach ($newest_performances as $performace) {
	display_performance($performace);
}

?>


<?php

//FINISH UP TEMPLATE
echo template_bottom();

if (isset($conn)) {$conn->close();} ?>
