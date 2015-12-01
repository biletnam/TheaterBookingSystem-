<?php
//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.
include('debugging.php');//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.

include('database/db_properties.php'); //gets the local db settings
include('database/DB.php'); //for the DB

$DB = new DB($servername, $username, $password, $dbname);
$DB->connect($recreate_database_from_new);

include('template.php');

//SET PAGE VARIABLES
$page_title = "Caspar's Theater::Home Page";
$page_description = "Home Page of Caspar's Theatre, come here to see the best shows in town. Online Booking Available NOW!";
$current_page = "index";

//START THE TEMPALTE
$Template = new Template($page_title, $page_description, $current_page);

$Template->pre_content();

//NOW FOR THE CONTENT

function var_from_POST_or_GET($var_name){
	if (isset($_POST[$var_name])){
		return $_POST[$var_name];
	}
	elseif (isset($_GET[$var_name])){
		return $_GET[$var_name];
	}
	else {
		return NULL;
	}
}
$pid = intval(var_from_POST_or_GET('pid'));
if ($pid != NULL) {
	$performance = $DB->getPerformance($pid);
	if (isset($_POST['numseats'])) {
		$num_seats = intval($_POST['numseats']);
		$seats = array();
		foreach ($range(1,$numseats) as $i){
			array_push($_POST["seat$i"]);
		}
		
		$cn = var_from_POST_or_GET('cn');
		
		$Template->process_booking_form($performance[0], $seats, $cn, $DB);
	}
	else {
		$Template->display_booking_form($DB, $performance[0]);
	}
	
}
else {
	echo "<p>To make a booking please, go to the upcoming performances page and choose which show you would like to book.</p>";
}

//FINISH UP TEMPLATE
$Template->post_content();

$DB->close();

?>