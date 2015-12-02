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

if (isset($_GET['pid'])){
	//////////////////
	//it was a get reqest, we should display the booking form
	//////////////////
	$performance = $DB->getPerformance(intval($_GET['pid']));
	$Template->display_booking_form($DB,$performance[0]);
}
elseif (isset($_POST['pid'])){
	//////////////////
	//it was a post request, we should process the data
	//////////////////
	$performance = $DB->getPerformance(intval($_POST['pid']));
	$all_seats = $DB->getAllSeats();
	$selected_seats = array();
	foreach ($all_seats as $i => $row_no){
		if (isset($_POST[$row_no])){
			if ($_POST[$row_no] == "1"){
				array_push($selected_seats, $row_no);
			}
		}
	}
	$customer_name = NULL;
	if (isset($_POST['customer_name'])){
		$customer_name = $_POST['customer_name'];
	}
	$Template->process_booking_form($performance[0], $selected_seats, $customer_name, $DB);
}
else {
	//////////////////
	//no performance selected, display performances.
	//////////////////
	echo "
	<div class=\"post highlighted\">
	<img src=\"images/logo.png\" height=\"100\" align=\"left\">
	<h2>Bookings</h2>
	<p>To make a booking please choose one of the shows below, and click Book Now!</p>
	</div>";
	$next_shows = $DB->getNextPerformances(50);
	foreach ($next_shows as $show) {
		$Template->display_performance($show, $DB);
	}
}

//FINISH UP TEMPLATE
$Template->post_content();

$DB->close();

?>