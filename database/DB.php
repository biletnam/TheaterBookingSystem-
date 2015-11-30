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
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->connected = TRUE;
			if ($recreate_database_from_new) {
				$this->dropDB();
				$this->makeDB();
				$this->populateDB();
				return $this->connect(FALSE);
			}
			return TRUE;
		}
		catch (PDOException $e) {
			echo "PDOException: ".$e->getMessage();
			return FALSE;
		}
	}

	private function dropDB() {
		$this->conn->query("DROP DATABASE $this->dbname;");
	}

	private function makeDB() {
		$this->conn->query("CREATE DATABASE IF NOT EXISTS $this->dbname;");
	}
	
	private function populateDB() {

		if (!$this->connected) {die("must connect first");}
		$sql_files = array (
		//ORDERING HERE MATTERS
		//this is the order they will be executed in.
		//So you must make sure it can run.
			"database/tables.sql",
			"database/zone.sql",
			"database/seat.sql",
			"database/productions.sql",
			"database/performances.sql"
		);
		
		foreach ($sql_files as $file) {
			$this->runSqlFile($file);
		}
	
	}
	
	private function runSqlFile($filename) {
		if (!$this->connected) {die("must connect first");}
		$file = file($filename);
		foreach ($file as $line_num => $sql_command) {
			$this->unsafe_query($sql_command);
		}
	}
	
	private function unsafe_query($sql) {
		if(!$this->connected) {die("must connect first");}
		return $this->conn->query($sql);
		
	}
	
	//This function is public, but you are encourged to
	//make a more robust preset version below as a preset
	//and if apprioprate use permenant prepared query.
	public function query($sql, $params) {
		if (!$this->connected) {die("must connect first");}
		$prepared_query = $this->conn->prepare($sql);
		$prepared_query->execute($params);
		return $prepared_query->fetchAll();
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

	public function executePreparedQuery($name, $params){
		if (array_key_exists($name, $this->prepared_quieres) && 
			$this->connected){
			$this->prepared_quieres[$name]->execute($params);
			return $this->prepared_quieres[$name]->fetchAll();
		}
	}
	
	//////////////////////////////
	//PRESET QUERIES
	//////////////////////////////
	//if a query is going to be used more than once per page
	//then store the prepared query
	//if it's only once, there's probably not much point.
	
	public function getNewestProductions($limit = 10) {

		$sql = "SELECT DISTINCT pr.* 
			FROM Production pr
			  JOIN 
			  Performance pe on pe.title = pr.title
			ORDER BY pe.date_time DESC
			LIMIT 0,:lim;";
		$params = array(":lim" => $limit);

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
	
	public function getProductionsNextPerformances($production_title, $limit=3) {
		//check if it's already been prepared
		$this_query_name = "#getProductionsNextPerformances";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)) {
			$sql = "SELECT * 
				FROM Performance
				WHERE 
					Performance.title = :title
					AND
					Performance.date_time > (SELECT current_date)
				ORDER BY Performance.date_time
				LIMIT :lim;";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		//build the params		
		$params = array(":title" => $production_title,
						":lim" => $limit);
		//return the results
		return $this->executePreparedQuery($this_query_name, $params);
	}

	public function getNextPerformances($limit=10) {
		$sql = "SELECT s.*,
						p.description, p.mins, p.genre
				FROM Performance s
					JOIN Production p ON p.title = s.title
				WHERE s.date_time > (SELECT current_date)
				ORDER BY s.date_time ASC
				LIMIT :lim;";
		$params = array(":lim" => $limit);

		return $this->query($sql, $params);
	}

	public function getNumTicketsAvailable($performance_id) {
		$this_query_name = "#getNumTicketsAvailable";
		if (!array_key_exists($this_query_name, $this->prepared_quieres)){
			$sql = "SELECT COUNT(SELECT row_no FROM Seat)-COUNT(
				SELECT row_no FROM Booking JOIN Performance ON
				Booking.date_time = Performance.date_time
				WHERE Performance.id = :perfID
				) as available;";
			//prepare
			$this->makePreparedQuery($this_query_name, $sql);
		}
		$params = array(":perfID" => $performance_id);

		return $this->executePreparedQuery($this_query_name, $params);
	}
}?>
