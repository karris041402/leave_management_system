<?php

namespace App\Models;

use PDO;

class UserModel extends BaseModel
{
    protected function setTable(): void
    {
        $this->table = 'users';
    }

    /**
     * Find a user by username.
     * @param string $username
     * @return array|false
     */
    public function findByUsername(string $username): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->executeStatement($sql, ['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user.
     * @param string $employeeName
     * @param string $username
     * @param string $passwordHash
     * @param string $role
     * @return int|false The ID of the new user or false on failure.
     */
    public function create(string $employeeName, string $username, string $passwordHash, string $role = 'employee'): int|false
    {
        $sql = "INSERT INTO {$this->table} (employee_name, username, password_hash, role) VALUES (:employee_name, :username, :password_hash, :role)";
        $params = [
            'employee_name' => $employeeName,
            'username' => $username,
            'password_hash' => $passwordHash,
            'role' => $role
        ];
        $stmt = $this->executeStatement($sql, $params);
        return $stmt->rowCount() > 0 ? $this->db->lastInsertId() : false;
    }
}
