<?php

include('debugging.php');//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.

include('database/db_properties.php'); //gets the local db settings
include('database/DB.php'); //for the DB

$DB = new DB($servername, $username, $password, $dbname);
$DB->connect($recreate_database_from_new);

include('template.php');

//SET PAGE VARIABLES
$page_title = "Productions at Caspar's Theater";
$page_description = "A list of all the productions available at this magnificant place. Here!";
$current_page = "productions";

//START THE TEMPALTE
$Template = new Template($page_title, $page_description, $current_page);

$Template->pre_content();

//NOW FOR THE CONTENT
if (!isset($_GET['production'])) {
	$newest_productions = $DB->getNewestProductions(10);

	foreach ($newest_productions as $production) {
		$Template->display_production($production, 3, TRUE, $DB);
	}

} //$_GET['productions'] is not set

else {
	$production = $DB->getProductionByURL($_GET["production"]);
	$Template->display_production($production[0], 30, TRUE, $DB);
}

//FINISH UP TEMPLATE
$Template->post_content();

$DB->close();

?>
