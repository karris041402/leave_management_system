<?php

namespace App\Models;

use PDO;

class LeaveTypeModel extends BaseModel
{
    protected function setTable(): void
    {
        $this->table = 'leave_types';
    }

    /**
     * Insert initial leave types from the requirements.
     * @param array $leaveTypes
     * @return bool
     */
    public function insertInitialTypes(array $leaveTypes): bool
    {
        $sql = "INSERT INTO {$this->table} (code, description, point_value) VALUES (:code, :description, :point_value) ON DUPLICATE KEY UPDATE description=VALUES(description), point_value=VALUES(point_value)";
        $stmt = $this->db->prepare($sql);

        try {
            $this->db->beginTransaction();
            foreach ($leaveTypes as $type) {
                $stmt->execute([
                    'code' => $type['code'],
                    'description' => $type['description'],
                    'point_value' => $type['point_value']
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Failed to insert initial leave types: " . $e->getMessage());
            return false;
        }
    }
}
