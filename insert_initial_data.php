<?php
require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use App\Models\UserModel;
use App\Models\LeaveTypeModel;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Build Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/src/config/dependencies.php');
$container = $containerBuilder->build();

// --- Insert Test User ---
/** @var UserModel $userModel */
$userModel = $container->get(UserModel::class);

$testUsername = 'admin';
$testPassword = 'password';
$testEmployeeName = 'System Administrator';
$testRole = 'admin';

if (!$userModel->findByUsername($testUsername)) {
    $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
    $userId = $userModel->create($testEmployeeName, $testUsername, $hashedPassword, $testRole);
    if ($userId) {
        echo "Test user '{$testUsername}' created successfully with ID: {$userId}\n";
    } else {
        echo "Failed to create test user.\n";
    }
} else {
    echo "Test user '{$testUsername}' already exists.\n";
}

// --- Insert Initial Leave Types ---
/** @var LeaveTypeModel $leaveTypeModel */
$leaveTypeModel = $container->get(LeaveTypeModel::class);

$initialLeaveTypes = [
    ['code' => 'SL', 'description' => 'Sick Leave', 'point_value' => 1.000],
    ['code' => 'VL', 'description' => 'Vacation Leave', 'point_value' => 1.000],
    ['code' => 'SPL', 'description' => 'Special Leave', 'point_value' => 1.000],
    ['code' => 'HD', 'description' => 'Half Day', 'point_value' => 0.500],
    ['code' => 'ABS', 'description' => 'Absent', 'point_value' => 1.000],
    // Add other types from the image/requirements
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

if ($leaveTypeModel->insertInitialTypes($initialLeaveTypes)) {
    echo "Initial leave types inserted/updated successfully.\n";
} else {
    echo "Failed to insert initial leave types.\n";
}
