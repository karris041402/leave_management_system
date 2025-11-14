<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

// Publicly accessible welcome route
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(['message' => 'Welcome to the Employee Leave Management API']));
    return $response->withHeader('Content-Type', 'application/json');
});

// --- Setup Route (Unprotected) ---
$app->post('/api/setup/seed', \App\Controllers\SetupController::class . ':seedDatabase');

// --- Authentication Route ---
$app->post('/api/auth/login', \App\Controllers\AuthController::class . ':login');

// --- API Routes with Authentication Middleware ---
$app->group('/api', function (RouteCollectorProxy $group) {

    // --- User Routes (Protected) ---
    $group->get('/users', \App\Controllers\UserController::class . ':index')
        ->add(new RoleMiddleware(['admin', 'encoder']));
    // Add other user CRUD endpoints here (e.g., POST /users, GET /users/{id}, etc.)

    // --- Leave Routes (Protected) ---
    $group->get('/leave-types', \App\Controllers\LeaveController::class . ':getLeaveTypes');
    $group->get('/leaves/user/{user_id}/month/{month_year}', \App\Controllers\LeaveController::class . ':getMonthlyRecords');
    $group->post('/leaves/save', \App\Controllers\LeaveController::class . ':saveMonthlyRecords')
        ->add(new RoleMiddleware(['admin', 'encoder']));

})->add(new AuthMiddleware());
