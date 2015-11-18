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
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	include('prepare_database.php');
}
else {echo "connection successfull2";}

$create_tables = array();
$create_tables[0] = "CREATE TABLE tarea(
 name char(12) not null,
 price_multiplier float not null default 1.0, 
 primary key (name)
 ); ";

$create_tables[1] = "CREATE TABLE seat(
 row_no char(3) not null,
 area_name char(12) not null,
 primary key (row_no),
 foreign key (area_name) references tarea(name)
);";

$create_tables[2] = "CREATE TABLE production(
 title varchar(100) not null,
 basic_price numeric(5,2) not null,
 primary key (title)
);";

$create_tables[3] = "CREATE TABLE performance(
 date_time datetime not null,
 title varchar(100) not null,
 primary key (date_time),
 foreign key (title) references production(title)
);";

$create_tables[4] = "CREATE TABLE booking(
 ticket_no mediumint not null AUTO_INCREMENT,
 row_no char(3) not null,
 date_time datetime not null,
 customer_name varchar(300) not null,
 primary key (ticket_no),
 foreign key (row_no) references seat(row_no),
 foreign key (date_time) references performance(date_time)
);";

foreach ($create_tables as $table)
	if ($conn->query($table) === TRUE) {echo "created successfully!";} 
	else {
	    echo "could not perform $table";
	}


?>