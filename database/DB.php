<?php

class DB {
	private $host = "localhost";
	private $username = "root";
	private $dbname = "Theater";
	private $password = "";
	
	private $conn;
	private $connected = FALSE;
	private $prepared_quieres = array();

	public function __construct($s, $u, $p, $d) {
		$this->host = $s;
		$this->username = $u;
		$this->password = $p;
		$this->dbname = $d;
	}

	public function __destruct(){
		$this->close();
	}
	
	public function close() {
		$conn = null;
	}
	
	public function connect($recreate_database_from_new = FALSE) {
		try {
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connected = TRUE;
			if ($recreate_database_from_new) {
				$this->dropDB();
				$this->makeDB();
				$this->selectDB();
				$this->populateDB();
			}
		}
		catch (PDOException $e) {
			echo "PDOException: ".$e->getMessage();
			$this->connected = FALSE;
		}
	}

	private function dropDB() {
		$this->conn->query("DROP DATABASE $this->dbname;");
		echo "<br>dropped";
	}

	private function makeDB() {
		$this->conn->query("CREATE DATABASE IF NOT EXISTS $this->dbname;");
		echo "<br>makeDB";
	}

	private function selectDB() {
		$this->conn->query("USE $this->dbname;");
		echo "<br>selected";
	}
	
	private function populateDB() {
		echo "<br>populating";
		$sql_files = array (
		//ORDERING HERE MATTERS
		//this is the order they will be executed in.
		//So you must make sure it can run.
			"database/tables.sql",
			"database/zone.sql",
			"database/seat.sql",
			"database/productions.sql",
			"database/performances.sql",
			"database/bookings.sql"
		);
		
		foreach ($sql_files as $file) {
			$this->runSqlFile($file);
			echo "<br>$file was run";
		}

		echo "<br>populated";
	
	}
	
	private function runSqlFile($filename) {
		if (!$this->connected) {die("Cannot Run SQL File: Not Connected");}
		$sql = file_get_contents($filename);
		echo "$sql";
		$this->unsafe_query($sql, FALSE);
	}
	
	////////////////
	//QUERY FUNCTIONS
	////////////////
	
	private function unsafe_query($sql, $RETURN_RESULTS = TRUE) {
		if(!$this->connected) {die("Cannot Run Unprepared Query: Not Connected");}
		$results = $this->conn->query($sql);
		if ($RETURN_RESULTS){
			return $results->fetchAll();
		}
		
	}
	
	//This function is public, but you are encourged to
	//make a more robust preset version below as a preset
	//and if apprioprate use permenant prepared query.
	public function query($sql, $params) {
		if (!$this->connected) {die("Cannot Run Prepared Query: Not Connected");}
		$prepared_query = $this->conn->prepare($sql);
		foreach ($params as $name => $value){
			$this->bindParam($prepared_query, $name, $value);
		}
		$prepared_query->execute();
		return $prepared_query->fetchAll();
	}

	private function bindParam($prepared_statement, $name, $value){
		switch (gettype($value)) {
			case 'string':
				$prepared_statement->bindParam($name, $value, PDO::PARAM_STR, strlen($value));
				break;
			case 'integer':
				$prepared_statement->bindValue($name, $value, PDO::PARAM_INT);
				break;
			default:
				die("unknown PDO type found");
		}
	}

	public function makePreparedQuery($name, $prepared_sql){
		//will not overwrite a name already used
		if (!array_key_exists($name, $this->prepared_quieres) && 
			$this->connected){
			$prepared_query = $this->conn->prepare($prepared_sql);
			$this->prepared_quieres[$name]=$prepared_query;
			return TRUE;
		}
		return FALSE;
	}

	public function executePreparedQuery($name, $params, $RETURN_RESULTS = TRUE){
		if (array_key_exists($name, $this->prepared_quieres) && 
			$this->connected){
			$this->prepared_quieres[$name]->execute($params);
			if($RETURN_RESULTS){
				return $this->prepared_quieres[$name]->fetchAll();
			}
		}
	}
	
	//////////////////////////////
	//PRESET QUERIES
	//////////////////////////////
	//if a query is going to be used more than once per page
	//then store the prepared query
	//if it's only once, there's probably not much point.
	
	public function getNewestProductions() {

		$sql = "SELECT DISTINCT pr.* 
			FROM Production pr
			  JOIN 
			  Performance pe on pe.title = pr.title
			ORDER BY pe.date_time DESC";
		$params = array();

		 //todo get it to find the next performance
	//todo get it to find out when it's first performance was/is
	//make it only choose performances that have a performance in the future
		
		return $this->query($sql, $params);
	}

	public function getProductionByURL($url) {
		$sql = "SELECT *
				FROM Production 
				WHERE Production.url = :url;";
		$params = array(":url" => $url);

		return $this->query($sql, $params);
	}
	
	public function getProductionsNextPerformances($production_title) {
		//check if it's already been prepared
		$this_query_name = "#getProductionsNextPerformances";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)) {
			$sql = "SELECT * 
				FROM Performance
				WHERE 
					Performance.title = :title
					AND
					Performance.date_time > (SELECT current_date)
				ORDER BY Performance.date_time";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		//build the params		
		$params = array(":title" => $production_title);
		//return the results
		return $this->executePreparedQuery($this_query_name, $params);
	}

	public function getNextPerformances() {
		$sql = "SELECT s.*,
						p.description, p.mins, p.genre
				FROM Performance s
					JOIN Production p ON p.title = s.title
				WHERE s.date_time > (SELECT current_date)
				ORDER BY s.date_time ASC";
		$params = array();

		return $this->query($sql, $params);
	}
	
	public function getPerformance($pid){
		//check if it's already been prepared
		$this_query_name = "#getPerformances";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)) {
			$sql = "SELECT p.*, P.*
				FROM Performance p
				JOIN Production P
					ON p.title = P.title
				WHERE 
					p.id = :pid";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		//build the params		
		$params = array(":pid" => $pid);
		//return the results
		return $this->executePreparedQuery($this_query_name, $params);		
	}

	public function getTicketsAvailable($performance_id) {
		$this_query_name = "#getNumTicketsAvailable";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)){
			$sql = 
	"SELECT 
		s.row_no,
		s.zone_name,
		z.description,
		ROUND(z.price_multiplier *
			(
				SELECT
					P.base_price
				FROM
					Production P 
					JOIN Performance p
						ON P.title = p.title
				WHERE
					p.id = :perfID
			)
			, 2) as price
	FROM
		Seat s
		JOIN Zone z
			ON s.zone_name = z.name
	WHERE
		s.row_no NOT IN 
			(
				SELECT 
					s.row_no
				FROM
					Seat s
					JOIN Booking b 
						ON s.row_no = b.row_no
				WHERE
					b.performance_id = :perfID
			)
	ORDER BY
		CHAR_LENGTH(s.zone_name) DESC, price ASC
			";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		$params = array(":perfID" => $performance_id);

		$raw = $this->executePreparedQuery($this_query_name, $params);
		
		//process teh results for the form
		
		
		return $raw;
	}

	public function getTicketsAvailableByZone($perfid){
		$raw = $this->getTicketsAvailable($perfid);
		$results = array();
		foreach ($raw as $seat){
			$row_no = $seat["row_no"];
			$row = substr($row_no, 0,1);
			$no = substr($row_no, 1);
			$zone = $seat["zone_name"];
			$price = $seat["price"];
			$description = $seat["description"];
			
			if (!array_key_exists($zone, $results)){
				//if new zone
				$results[$zone] = array("rows" => array(),
										"price" => $price,
										"description" => $description);
			}
			if (!array_key_exists($row, $results[$zone]["rows"])) {
				//if new row
				$results[$zone]["rows"][$row] = array();
				foreach (range(1,20) as $n){
					$results[$zone]["rows"][$row][$n]=FALSE;
				}
			}
			//add seat_no
			$results[$zone]["rows"][$row][intval($no)]=TRUE;
			
		}
		return $results;
	}

	public function getSoldTickets($performance_id){
		$this_query_name = "#getTicketsSold";
		if (!array_key_exists($this_query_name, $this->prepared_quieres) ){
			$sql = 
    "SELECT 
    	b.customer_name, 
    	b.row_no, 
    	s.zone_name, 
    	ROUND(P.base_price * z.price_multiplier, 2) as ticket_price,
    	p.title,
    	p.date_time
    FROM
    	Seat s
    	JOIN Booking b
    		ON s.row_no = b.row_no
    	JOIN Performance p
    		ON p.id = b.performance_id
    	JOIN Production P
    		ON p.title = P.title
    	JOIN Zone z
    		ON z.name = s.zone_name
    WHERE
    	s.row_no IN 
    		(
    			SELECT 
    				b.row_no 
    			FROM 
    				Booking b 
    			WHERE 
    				b.performance_id = :perfID
    		)
   	;";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		$params = array(":perfID" => $performance_id);

		return $this->executePreparedQuery($this_query_name, $params);

	}
	
	public function getAllSeats(){
		$sql = "SELECT row_no FROM Seat;";
		$raw = $this->unsafe_query($sql, TRUE);
		$results = array();
		foreach($raw as $row => $info){
			array_push($results, $info["row_no"]);
		}
		return $results;
		
	}
	
	public function getFilledSeats($perfid){
		$sql = "SELECT row_no from Booking WHERE performance_id = :pid;";
		return $this->query($sql, array(":pid" => $perfid));
	}

	public function seatBooked($pid, $row_no){
		//check if it's already been prepared
		$this_query_name = "#isSeatBooked";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)) {
			$sql = "SELECT * FROM Booking b WHERE b.performance_id = :pid AND b.row_no = :rn";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		//build the params		
		$params = array(":pid" => $pid, ":rn" => $row_no);
		//return the results
		$results = $this->executePreparedQuery($this_query_name, $params);
		if (sizeof($results) == 0){
			return FALSE;
		}
		return TRUE;
	}

	public function bookSeats($pid, $seats, $name, $email){
		//check if it's already been prepared
		$this_query_name = "#BookSeats";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)) {
			$sql = "INSERT into Booking values (NULL, :rn, :pid,  :name, :e);";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		//
		$all_fine = TRUE;
		foreach ($seats as $i => $row_no) {
				$params = array(":pid" => $pid, ":rn" => $row_no, ':name' => $name, ':e' => $email);
				try {
					$this->executePreparedQuery($this_query_name, $params, FALSE);
				}
				catch (Exception $ex){
					echo $ex->getMessage();
					$all_fine = FALSE;
				}
			}
		return $all_fine;
	}

	public function seatExists($row_no, $zone_name){
		//check if it's already been prepared
		$this_query_name = "#seatExists";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)) {
			$sql = "SELECT * FROM Seat WHERE row_no = :r AND zone_name = :z";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		//build the params		
		$params = array(":r" => $row_no, ":z" => $zone_name);
		//return the results
		$results = $this->executePreparedQuery($this_query_name, $params);
		if (sizeof($results) == 0){
			return FALSE;
		}
		return TRUE;		
	}
	
	public function getCostOfSeat($pid, $rn){
		//check if it's already been prepared
		$this_query_name = "#seatCost";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)) {
			$sql = 
			"SELECT
				ROUND (
				(
				SELECT
					P.base_price
				FROM
					Production P
					JOIN 
						Performance p ON P.title = p.title
					WHERE
						p.id = :p
				)
				*
				(
				SELECT
					z.price_multiplier
				FROM
					Zone z
					JOIN Seat s ON s.zone_name = z.name
				WHERE
					s.row_no = :rn					
				)
				, 2) as price";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		//build the params		
		$params = array(":p" => $pid, ":rn" => $rn);
		//return the results
		$results = $this->executePreparedQuery($this_query_name, $params);
		return $results[0]['price'];
	}
}?>
