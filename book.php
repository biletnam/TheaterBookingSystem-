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

function form($performance, $seats, $customer_name) {
	$title = $performance['title'];
	$dt = $performance['date_time'];
	echo "<form action=\"book.php\">
		Performance:<br>		
		<b>$title on $dt</b><br>
		Number of seats:<br>
		<select value=\"sizeof($seats)\">";
		
	foreach (range(1,10) as $num){
		echo "<option value=\"$num\">$num</option>";
	}
	echo "</select><br>";
	
	echo "<p>Here goes the code to choose the seats</p>";
	
	echo "Your Name:<br>
		<input type=\"text\" value=\"$customer_name\">
		<br><br>
		<input type=\"submit\" value=\"Book\">
		</form>";			
		
}

$fp = array("title" => "fmae", "date_time" => "dt");
$seats = NULL;
$cn = "C";
form($fp, $seats, $cn);


//FINISH UP TEMPLATE
$Template->post_content();

$DB->close();

?>