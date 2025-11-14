<?php
use DI\Container;

$container->set('db', function (Container $container) {
    $host = $_ENV['DB_HOST'];
    $db   = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
         return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
});

// Add other dependencies here (e.g., Logger, JWT Handler)

    // Models
    $container->set(\App\Models\UserModel::class, function (Container $container) {
        return new \App\Models\UserModel($container->get('db'));
    });
    $container->set(\App\Models\LeaveTypeModel::class, function (Container $container) {
        return new \App\Models\LeaveTypeModel($container->get('db'));
    });
    $container->set(\App\Models\LeaveRecordModel::class, function (Container $container) {
        return new \App\Models\LeaveRecordModel($container->get('db'));
    });
    $container->set(\App\Models\MonthlySummaryModel::class, function (Container $container) {
        return new \App\Models\MonthlySummaryModel($container->get('db'));
    });

    // Controllers
    $container->set(\App\Controllers\AuthController::class, function (Container $container) {
        return new \App\Controllers\AuthController($container->get(\App\Models\UserModel::class));
    });
    $container->set(\App\Controllers\UserController::class, function (Container $container) {
        return new \App\Controllers\UserController($container->get(\App\Models\UserModel::class));
    });
    $container->set(\App\Controllers\LeaveController::class, function (Container $container) {
        return new \App\Controllers\LeaveController(
            $container->get(\App\Models\LeaveTypeModel::class),
            $container->get(\App\Models\LeaveRecordModel::class),
            $container->get(\App\Models\MonthlySummaryModel::class)
        );
    });
    $container->set(\App\Controllers\SetupController::class, function (Container $container) {
        return new \App\Controllers\SetupController(
            $container->get(\App\Models\UserModel::class),
            $container->get(\App\Models\LeaveTypeModel::class)
        );
    });
// $container->set('logger', function (Container $container) { ... });
