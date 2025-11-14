<?php

namespace App\Models;

use PDO;

class LeaveRecordModel extends BaseModel
{
    protected function setTable(): void
    {
        $this->table = 'leave_records';
    }

    /**
     * Get all leave records for a specific user and month.
     * @param int $userId
     * @param string $monthYear 'YYYY-MM' format
     * @return array
     */
    public function findByUserAndMonth(int $userId, string $monthYear): array
    {
        $sql = "
            SELECT 
                lr.leave_date, 
                lt.code, 
                lt.description, 
                lt.point_value
            FROM {$this->table} lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.user_id = :user_id 
            AND DATE_FORMAT(lr.leave_date, '%Y-%m') = :month_year
            ORDER BY lr.leave_date ASC
        ";
        $params = [
            'user_id' => $userId,
            'month_year' => $monthYear
        ];
        $stmt = $this->executeStatement($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save multiple leave records for a user.
     * @param int $userId
     * @param array $records Array of ['date' => 'YYYY-MM-DD', 'leave_type_id' => 1]
     * @return bool
     */
    public function saveRecords(int $userId, array $records): bool
    {
        // Delete existing records for the month to allow for "Render Month" (overwrite) functionality
        if (empty($records)) {
            return true; // Nothing to save
        }

        $firstDate = $records[0]['date'];
        $monthYear = date('Y-m', strtotime($firstDate));

        $deleteSql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND DATE_FORMAT(leave_date, '%Y-%m') = :month_year";
        $deleteStmt = $this->db->prepare($deleteSql);
        $deleteStmt->execute(['user_id' => $userId, 'month_year' => $monthYear]);

        // Insert new records
        $insertSql = "INSERT INTO {$this->table} (user_id, leave_date, leave_type_id) VALUES (:user_id, :leave_date, :leave_type_id)";
        $insertStmt = $this->db->prepare($insertSql);

        try {
            $this->db->beginTransaction();
            foreach ($records as $record) {
                $insertStmt->execute([
                    'user_id' => $userId,
                    'leave_date' => $record['date'],
                    'leave_type_id' => $record['leave_type_id']
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Failed to save leave records: " . $e->getMessage());
            return false;
        }
    }
}
