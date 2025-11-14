<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct()
    {
        // Load environment variables
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'leave_management_db';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? 'password';
    }

    /**
     * Get the database connection
     * @return PDO
     */
    public function getConnection(): PDO
    {
        $this->conn = null;

        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            // In a real application, you would log this error instead of echoing it
            error_log("Connection error: " . $exception->getMessage());
            throw new \Exception("Database connection failed: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
