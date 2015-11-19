<?php
$servername = "dragon.ukc.ac.uk";
$username = "cn249";
$password = "zbrandh";
$dbname = "cn249";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	include('prepare_database.php');
}
else {}

?> 