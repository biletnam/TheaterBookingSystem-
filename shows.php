<?php
//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.
include('debugging.php');//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.

include('database/db_properties.php'); //gets the local db settings
include('database/DB.php'); //for the DB

$DB = new DB($servername, $username, $password, $dbname);
$DB->connect($recreate_database_from_new);

include('template.php');

//SET PAGE VARIABLES
$page_title = "Caspar's Theater::Shows";
$page_description = "Come See our Lovely shows!";
$current_page = "shows";

//START THE TEMPALTE
$Template = new Template($page_title, $page_description, $current_page);

$Template->pre_content();

//NOW FOR THE CONTENT

if (!isset($_GET["show"])){
	$next_shows = $DB->getNextPerformances(50);
	foreach ($next_shows as $show) {
		$Template->display_performance($show, $DB);
	}

}//endif

else {
	echo "this show";
}

//FINISH UP TEMPLATE
$Template->post_content();

$DB->close();

?>