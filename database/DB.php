<?php

class DB {
	private $host = "localhost";
	private $username = "root";
	private $dbname = "Theater";
	private $password = "";
	
	private $conn;
	private $connected = FALSE;

	public function __construct($s, $u, $p, $d) {
		$this->host = $s;
		$this->username = $u;
		$this->password = $p;
		$this->dbname = $d;
	}

	public function __destruct(){
		closeConnection();
	}
	

	public function closeConnection() {
		$conn = null;
	}
	
	public function connect($recreate_database_from_new) {
		try {
			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connected = TRUE;
			if ($recreate_database_from_new) {
				dropDB();
				makeDB();
				populateDB();
			}
			return TRUE;
		}
		catch (PDOException $e) {
			echo "PDOException: ".$e.getMessage();
			return FALSE;
		}
	}

	private function dropDB() {
		$this->$conn->query("DROP DATABASE $dbname;");
	}

	private function makeDB() {
		$this->$conn->query("CREATE DATABASE IF NOT EXISTS $dbname;");
	}
	
	private function populateDB() {

		if (!$connected) {die("must connect first");}
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
			runSqlFile($filename);
		}
	
	}
	
	private function run_sql_file($filename) {
		if (!$connected) {die("must connect first");}
		$file = file($filename);
		foreach ($file as $line_num => $sql_command) {
			unsafe_query($sql_command);
		}
	}
	
	private function unsafe_query($sql) {
		if(!$this->connected) {die("must connect first");}
		return $this->conn->query($sql);
		
	}
	
	//This function is public, but you are encourged to
	//make a more robust preset version below
	public function query($sql, $params) {
		if (!$this->connected) {die("must connect first");}
		$prepared_query = $this->conn->prepare($sql);
		return $prepared_query->execute($params);
	}
	
	//////////////////////////////
	//PRESET QUERIES
	//////////////////////////////
	
	public function getNewestProductions($limit = 10) {
		$sql = "SELECT DISTINCT pr.* 
			FROM Production pr
			  JOIN 
			  Performance pe on pe.title = pr.title
			ORDER BY pe.date_time DESC
			LIMIT :n;";
		$params = array(":n" => $limit);

		 //todo get it to find the next performance
	//todo get it to find out when it's first performance was/is
	//make it only choose performances that have a performance in the future
		
		return $this->query($sql, $params);
	}
	
	public function getProductionsNextPerformances($production_title, $limit=3) {
		$sql = "SELECT * 
		FROM Performance
		WHERE 
			Performance.title = :p
			AND
			Performance.date_time > (SELECT current_date)
		ORDER BY Performance.date_time
		LIMIT :n;";
		
		$params = array(":p" => $production_title,
						":n" => $limit);
		
		return $this->query($sql, $params);
	}
}

?>