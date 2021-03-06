<?php

//DEBUGGING CODE TO DELETE THE DATABASE
function file_insert_into_database($file_name, $conn) {
	$file = file($file_name);
	foreach ($file as $line_num => $insert) {
		if ($conn->query($insert)) {}
		else {
			echo "<br>could not add in <br> $insert <br>";
			echo $conn->error;
		}
	}
}

function create_default_database($conn, $dbname) {
	//safely create the db
	$create_db_safe = "CREATE DATABASE IF NOT EXISTS $dbname";
	if ($conn->query($create_db_safe) === TRUE) {}
	else {die("Error creating database: " . $conn->error);}

	//now we can connect to the database
	select_db($conn, $dbname);

	///////////populate db
	//define the tables
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
	 url varchar(30) not null,
	 basic_price numeric(5,2) not null,
	 mins smallint default 90,
	 genre varchar(30),
	 description text, 
	 primary key (title)
	);";

	$create_tables[3] = "CREATE TABLE Performance(
	 id mediumint not null AUTO_INCREMENT,
	 date_time datetime not null,
	 title varchar(100) not null,
	 primary key (id),
     unique (date_time),
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
	foreach ($create_tables as $table) {
		if ($conn->query($table) === TRUE) {
			//echo "created successfully! $table \n\n";
		} 
		else {
			echo "<br>could not perform <br> $table<br>";
			echo $conn->error;
		}
	}
	
	//insert other values from filesize
	//careful: the order matters.
	file_insert_into_database('database/zone.sql', $conn);
	file_insert_into_database('database/seat.sql', $conn);
	file_insert_into_database('database/productions.sql', $conn);
	file_insert_into_database('database/performances.sql', $conn);
}

?>