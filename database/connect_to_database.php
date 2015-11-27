<?php
include('db_properties.php');

function connect_to_database($servername, $username, $password, $dbname){
	$conn = PDO("mysql:host=$host;dbname=$dbname", $user, $pwd);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $conn;
}

function connect_to_server($servername, $username, $password){
	$conn = newPDO("mysql:host=$host", $user, $pwd);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $conn

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
	$conn->query("USE $dbname");
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
		die("Could not connect to database". $conn->connect_error. "Perhaps run with new_db = YeS");
	}
}

?> 