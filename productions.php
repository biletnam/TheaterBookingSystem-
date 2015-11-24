<?php
//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.
$debug = TRUE;
$recreate_database_from_new = ($debug && isset($_GET["new_db"]) && $_GET["new_db"]=="YeS") ? TRUE : FALSE;

include('database/connect_to_database.php'); //connection closed at the end of this file

//SET PAGE VARIABLES
$page_title = "Productions at Caspar's Theater";

$page_description = "A list of all the productions available at this magnificant place. Here!";

$current_page = "productions";

//DO PAGE QUERIES
$newest_performances_sql = "
SELECT DISTINCT pr.* 
FROM Production pr
  JOIN Performance pe on pe.title = pr.title
ORDER BY pe.date_time DESC
LIMIT 10;
"; //todo get it to find the next performance
//todo get it to find out when it's first performance was/is
//make it only choose performances that have a performance in the future


$newest_performances = $conn->query($newest_performances_sql);


//START THE TEMPALTE
include('template.php');

echo template_top($page_title, $page_description, menu($menu_items, $current_page));

//NOW FOR THE CONTENT
?>

<?php

function display_performance($performance){
	global $conn;
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
		<h3>Next Performances</h3>
		<ul>";
		
	$next_5_performances_sql = "
	SELECT * 
	FROM Performance
	WHERE 
		Performance.title = '$title'
		AND
		Performance.date_time > (SELECT current_date)
	ORDER BY Performance.date_time;
	LIMIT 5;
	";
	
	$next_performances = $conn->query($next_5_performances_sql);
	var_dump($next_performances);
	foreach($next_performances as $show) {
		$date = date($show['date_time']);
		$link = "shows.php?show=".$show['date_time'];
		echo "<li><a href=\"$link\">$date</a></li>";
	}
	echo "		
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
