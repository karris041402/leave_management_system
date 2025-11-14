<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UserModel;

class UserController
{
    private UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function index(Request $request, Response $response): Response
    {
        // Role-based access control: Only 'admin' or 'encoder' can view all users
        $token = $request->getAttribute('token');
        if (!in_array($token['role'], ['admin', 'encoder'])) {
            return $this->errorResponse($response, 'Access denied. Insufficient role.', 403);
        }

        $users = $this->userModel->findAll();

        // Sanitize output: remove password hash
        $safeUsers = array_map(function($user) {
            unset($user['password_hash']);
            return $user;
        }, $users);

        $response->getBody()->write(json_encode($safeUsers));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Add other CRUD methods (create, update, delete) here...

    private function errorResponse(Response $response, string $message, int $status): Response
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
