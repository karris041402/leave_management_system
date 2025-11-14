<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UserModel;
use App\Models\LeaveTypeModel;

class SetupController
{
    private UserModel $userModel;
    private LeaveTypeModel $leaveTypeModel;

    public function __construct(UserModel $userModel, LeaveTypeModel $leaveTypeModel)
    {
        $this->userModel = $userModel;
        $this->leaveTypeModel = $leaveTypeModel;
    }

    public function seedDatabase(Request $request, Response $response): Response
    {
        $logs = [];

        // --- Insert Test User ---
        $testUsername = 'admin';
        $testPassword = 'password';
        $testEmployeeName = 'System Administrator';
        $testRole = 'admin';

        if (!$this->userModel->findByUsername($testUsername)) {
            $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
            $userId = $this->userModel->create($testEmployeeName, $testUsername, $hashedPassword, $testRole);
            $logs[] = $userId ? "Test user '{$testUsername}' created successfully." : "Failed to create test user.";
        } else {
            $logs[] = "Test user '{$testUsername}' already exists.";
        }

        // --- Insert Initial Leave Types ---
        $initialLeaveTypes = [
            ['code' => 'SL', 'description' => 'Sick Leave', 'point_value' => 1.000],
            ['code' => 'VL', 'description' => 'Vacation Leave', 'point_value' => 1.000],
            ['code' => 'SPL', 'description' => 'Special Leave', 'point_value' => 1.000],
            ['code' => 'HD', 'description' => 'Half Day', 'point_value' => 0.500],
            ['code' => 'ABS', 'description' => 'Absent', 'point_value' => 1.000],
            ['code' => 'VL8', 'description' => 'Vacation Leave (8 hours)', 'point_value' => 1.000],
            ['code' => 'VL10', 'description' => 'Vacation Leave (10 hours)', 'point_value' => 1.250],
            ['code' => 'VHD', 'description' => 'Vacation Half Day', 'point_value' => 0.500],
            ['code' => 'SL8', 'description' => 'Sick Leave (8 hours)', 'point_value' => 1.000],
            ['code' => 'SL10', 'description' => 'Sick Leave (10 hours)', 'point_value' => 1.250],
            ['code' => 'SPL1', 'description' => 'Special Leave 1', 'point_value' => 1.000],
            ['code' => 'SPL2', 'description' => 'Special Leave 2', 'point_value' => 1.000],
            ['code' => 'SPL3', 'description' => 'Special Leave 3', 'point_value' => 1.000],
            ['code' => 'CTO', 'description' => 'Compensatory Time Off', 'point_value' => 1.000],
            ['code' => 'M/FL8', 'description' => 'Maternity/Paternity/Family Leave (8 hours)', 'point_value' => 1.000],
            ['code' => 'M/FL10', 'description' => 'Maternity/Paternity/Family Leave (10 hours)', 'point_value' => 1.250],
            ['code' => 'SHD', 'description' => 'Special Half Day', 'point_value' => 0.500],
            ['code' => 'AB8', 'description' => 'Absence (8 hours)', 'point_value' => 1.000],
            ['code' => 'AB10', 'description' => 'Absence (10 hours)', 'point_value' => 1.250],
        ];

        if ($this->leaveTypeModel->insertInitialTypes($initialLeaveTypes)) {
            $logs[] = "Initial leave types inserted/updated successfully.";
        } else {
            $logs[] = "Failed to insert initial leave types.";
        }

        $response->getBody()->write(json_encode(['message' => 'Database seeding complete.', 'logs' => $logs]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
