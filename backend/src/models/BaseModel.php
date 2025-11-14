<?php

namespace App\Models;

use PDO;
use PDOException;

abstract class BaseModel
{
    protected PDO $db;
    protected string $table;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->setTable();
    }

    /**
     * Abstract method to set the table name for the model.
     */
    abstract protected function setTable(): void;

    /**
     * Find a record by its ID.
     * @param int $id
     * @return array|false
     */
    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all records.
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute a prepared statement with given parameters.
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    protected function executeStatement(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Log error and re-throw a generic exception
            error_log("Database Error in {$this->table}: " . $e->getMessage());
            throw new \Exception("Database operation failed.");
        }
    }
}
