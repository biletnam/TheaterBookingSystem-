    CREATE TABLE Zone(
    	name char(12) not null, 
    	price_multiplier float not null default 1.0, 
        description text,
    	primary key (name)
    	);

    CREATE TABLE Seat(
    	row_no char(3) not null, 
    	zone_name char(12) not null, 
    	primary key (row_no), 
    	foreign key (zone_name) 
    		references Zone(name)
    	);

    CREATE TABLE Production(
    	title varchar(100) not null, 
    	url varchar(30) not null unique, 
    	base_price numeric(5,2) not null, 
    	mins smallint default 90,
    	genre varchar(30), 
    	description text,  
    	primary key (title)
    	);

    CREATE TABLE Performance(
    	id mediumint not null AUTO_INCREMENT,
    	date_time datetime not null unique,
    	title varchar(100) not null, 
    	primary key (id), 
    	foreign key (title) 
    		references Production(title)
    	);

    CREATE TABLE Booking(
    	ticket_no mediumint not null AUTO_INCREMENT, 
    	row_no char(3) not null, 
    	performance_id mediumint not null, 
    	customer_name varchar(300) not null, 
    	primary key (ticket_no), 
    	foreign key (row_no) 
    		references Seat(row_no), 
    	foreign key (performance_id) 
    		references Performance(id)
    	);