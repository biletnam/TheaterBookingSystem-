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

//simple check
$see_tables = "SHOW TABLES";

$result = $conn->query($see_tables);
if ($result->num_rows != 5) {
	include('prepare_database.php');
}
var_dump($result);

foreach ($result as $r) {
	foreach ($r as $v) {
		echo $v;
	}
	echo "\n";
}

?> 