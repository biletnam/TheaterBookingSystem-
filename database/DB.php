<?php

class DB {
	private $host = "localhost";
	private $username = "root";
	private $dbname = "Theater";
	private $password = "";
	
	private $conn;
	private $connected = FALSE;
	
	public set_server($new_host, $new_username, $new_dbname, $new_password) {
		$this.host = $new_host;
		$this.username = $new_username;
		$this.dbname = $new_dbname;
		$this.password = $new_password;
		return connect();
	}
	
	public connect() {
		try {
			$this.conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$this.$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this.connected = TRUE;
			return TRUE;
		catch (PDOException $e) {
			echo "PDOException: ".$e.getMessage();
			return FALSE;
		}
	}
	
	public make_db() {
		if (!$connected) {die("must connect first");}
		$sql_files = (
			"database/tables.sql",
			"database/zone.sql",
			"database/seat.sql",
			"database/productions.sql",
			"database/performances.sql"
		)
		
		foreach ($sql_files as $file) {
			run_sql_file($filename);
		}
	
	}
	
	private run_sql_file($filename) {
		if (!$connected) {die("must connect first");}
		$file = file($filename);
		foreach ($file as $line_num = $sql_command) {
			unsafe_query($sql_command);
		}
	}
	
	private unsafe_query($sql) {
		if(!$connected) {die("must connect first");}
		return $conn->query($sql);
		
	}
	
	private query($sql, $params) {
		if (!$connected) {die("must connect first");}
		$conn->prepare($sql);
		return $conn->execute($params);
	}
	
	//////////////////////////////
	//PRESET QUERIES
	//////////////////////////////
	
	public getNewestProductions($limit = 10) {
		$sql = "SELECT DISTINCT pr.* 
			FROM Production pr
			  JOIN Performance pe on pe.title = pr.title
			ORDER BY pe.date_time DESC
			LIMIT :n";
		$params = array(":n" => $limit);
		
		return query($sql, $params);
	}
	
	public getProductionsNextPerformances($performance, $limit=3) {
		$sql = "SELECT * 
		FROM Performance
		WHERE 
			Performance.title = :p
			AND
			Performance.date_time > (SELECT current_date)
		ORDER BY Performance.date_time
		LIMIT :n";
		
		$params = array(":p" => $performance,
						":n" => $limit);
		
		return query($sql, $params);
	}
}

?>