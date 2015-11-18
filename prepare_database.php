<?php

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    //echo "Database created successfully";
} else {
   // echo "Error creating database: " . $conn->error;
}

//now we can connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

$create_tables = array();
$create_tables[0] = "CREATE TABLE Zone(
 name char(12) not null,
 price_multiplier float not null default 1.0, 
 primary key (name)
 ); ";

$create_tables[1] = "CREATE TABLE Seat(
 row_no char(3) not null,
 area_name char(12) not null,
 primary key (row_no),
 foreign key (area_name) references Zone(name)
);";

$create_tables[2] = "CREATE TABLE Production(
 title varchar(100) not null,
 basic_price numeric(5,2) not null,
 mins smallint default 90,
 genre varchar(30),
 description text, 
 primary key (title)
);";

$create_tables[3] = "CREATE TABLE Performance(
 date_time datetime not null,
 title varchar(100) not null,
 primary key (date_time),
 foreign key (title) references Production(title)
);";

$create_tables[4] = "CREATE TABLE Booking(
 ticket_no mediumint not null AUTO_INCREMENT,
 row_no char(3) not null,
 date_time datetime not null,
 customer_name varchar(300) not null,
 primary key (ticket_no),
 foreign key (row_no) references Seat(row_no),
 foreign key (date_time) references Performance(date_time)
);";

//create the tables
foreach ($create_tables as $table)
	if ($conn->query($table) === TRUE) {
		echo "created successfully! $table \n\n";} 
	else {
	    echo "could not perform $table\n\n";
	    echo $conn->connect_error;
	}

function file_insert_into_database($file_name) {
	$file = file($file_name);
	foreach ($file as $line_num => $insert) {
		if ($conn->query($insert)) {
			echo "added in $insert";
		}
		else {
			echo "could not add in $insert";
			echo $conn->connect_error;
		}
	}
}

file_insert_into_database('database/zone.sql');
file_insert_into_database('database/seat.sql');




?>