<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Theater";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	include('prepare_database.php');
}
else {}

?> 