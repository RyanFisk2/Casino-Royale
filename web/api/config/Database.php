<?php
class Database {
	// DB Credentials 
	// TODO make these env variables
	private $host = "localhost";
	private $db = "pokerdb";
	private $user = "root";
	private $pw = "Blackbelt2!";
	// Actual Connection Variable
	public $conn;

	public function getConnection() {

		$this->conn = null;

		try {
			$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->user, $this->pw);
		} catch (PDOException $exception) {
			echo "DB Connection Error: " . $exception->getMessage();
		}

		return $this->conn;
	}
}
?>
