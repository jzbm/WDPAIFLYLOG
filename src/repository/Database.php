<?php

class Database {
    private static $instance = null;
    private $connection;

    private $host = 'db';
    private $dbname = 'flylog';
    private $user = 'postgres';
    private $password = 'postgres';

    private function __construct() {
        try {
            $this->connection = new PDO(
                "pgsql:host={$this->host};dbname={$this->dbname}",
                $this->user,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function connect() {
        return $this->connection;
    }
}
