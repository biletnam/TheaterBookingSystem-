<?php
$servername = "dragon.ukc.ac.uk";
$username = "cn249";
$password = "zbrandh";
$dbname = "cn249";

// DEBUGIING CODE TO RECREATE A DATABASE
if (isset($recreate_database_from_new) && $recreate_database_from_new) {
	echo "recreating db";
	include('prepare_database.php');
	
}
else { //THE MORE USUAL CONNECT CODE
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		include('prepare_database.php');
	}
}

?> 