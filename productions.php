<?php
//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.

include('debugging.php');
include('database/db_properties.php');
include('database/DB.php');

$DB = new DB($servername, $username, $password, $dbname);
$DB->connect($recreate_database_from_new);

include('template.php');

//SET PAGE VARIABLES
$page_title = "Productions at Caspar's Theater";
$page_description = "A list of all the productions available at this magnificant place. Here!";
$current_page = "productions";

//START THE TEMPALTE
echo template_top($page_title, $page_description, menu($menu_items, $current_page));

//NOW FOR THE CONTENT
if (!isset($_GET['production'])) {
	$newest_productions = $DB->getNewestProductions(10);

	foreach ($newest_productions as $performance)
		$title = $performance['title'];
		$url = $performance['url'];
		$description = $performance['description'];
		$mins = $performance['mins'];
		$genre = $performance['genre'];

		$cover_image_src = "images/$title/cover.jpg";
		$coverimage = "<img src=\"$cover_image_src\" height=\"300\" align=\"right\">";

		echo "<div class=\"post\">
			$coverimage
			<h2><a href=\"productions.php?production=$url\">$title</a></h2>
			<p>$description</p>
			<ul class=\"performance-details\">
				<li>Runtime: $mins minutes</li>
				<li>Genre: $genre</li>
			</ul>
			<h3>Next Performances</h3>
			<ul>";


		$next_performances = $DB->getProductionsNextPerformances($title);
		$handle->bind_param("s", $title);
		$handle->execute();
		$next_performances = $handle->get_result();

		if ($next_performances){
			foreach($next_performances as $show) {
				$date = date('l, F jS o',strtotime(str_replace('-','/', $show['date_time'])));
				$link = "shows.php?show=".$show['id'];
				echo "<li><a href=\"$link\">$date</a></li>";
			}
		}
		echo "
		</div>";

} //$_GET['productions'] is not set

else { //$_GET['production'] is set
	echo "You looked for ".$_GET['production'];
}

//FINISH UP TEMPLATE
echo template_bottom();

$DB->close();

?>
