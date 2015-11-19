<?php

// $servername = "dragon.ukc.ac.uk";
// $username = "cn249";
// $password = "zbrandh";
// $dbname = "cn249";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Theater";

function connect_to_database($servername, $username, $password, $dbname){
	$conn = new mysqli($servername, $username, $password, $dbname);
	return $conn;
}

function connect_to_server($servername, $username, $password){
	return new mysqli($servername, $username, $password);
}

function connected($conn) {
	if ($conn->connect_error) {
		return FALSE;
	}
	return TRUE;
}

function drop_database($conn, $dbname){
	$conn->query("DROP DATABASE $dbname;");
}

function select_db($conn, $dbname) {
	$conn->select_db($dbname);
}

// DEBUGIING CODE TO RECREATE A DATABASE
if ($debug && $recreate_database_from_new) {
	echo "recreating db";
	$conn = connect_to_server($servername, $username, $password);
	drop_database($conn, $dbname);
	include('prepare_database.php');
	create_default_database($conn, $dbname);	
}
else { //THE MORE USUAL CONNECT CODE
	$conn = connect_to_database($servername, $username, $password, $dbname);
	if (!connected($conn)){
		die("Could not connect to database <br>". $conn->connect_error. "<br>Perhaps run with new_db = YeS");
	}
}

?> 