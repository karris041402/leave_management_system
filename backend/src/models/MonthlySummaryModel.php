<?php

namespace App\Models;

use PDO;

class MonthlySummaryModel extends BaseModel
{
    protected function setTable(): void
    {
        $this->table = 'monthly_summaries';
    }

    /**
     * Find a monthly summary for a user.
     * @param int $userId
     * @param int $month
     * @param int $year
     * @return array|false
     */
    public function findByUserMonthYear(int $userId, int $month, int $year): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND month = :month AND year = :year LIMIT 1";
        $params = [
            'user_id' => $userId,
            'month' => $month,
            'year' => $year
        ];
        $stmt = $this->executeStatement($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Save or update a monthly summary.
     * @param int $userId
     * @param int $month
     * @param int $year
     * @param float $vlBalance
     * @param float $slBalance
     * @return bool
     */
    public function saveOrUpdate(int $userId, int $month, int $year, float $vlBalance, float $slBalance): bool
    {
        $sql = "
            INSERT INTO {$this->table} (user_id, month, year, vacation_leave_balance, sick_leave_balance)
            VALUES (:user_id, :month, :year, :vl_balance, :sl_balance)
            ON DUPLICATE KEY UPDATE
                vacation_leave_balance = :vl_balance,
                sick_leave_balance = :sl_balance,
                updated_at = CURRENT_TIMESTAMP
        ";
        $params = [
            'user_id' => $userId,
            'month' => $month,
            'year' => $year,
            'vl_balance' => $vlBalance,
            'sl_balance' => $slBalance
        ];
        $stmt = $this->executeStatement($sql, $params);
        return $stmt->rowCount() > 0;
    }
}
