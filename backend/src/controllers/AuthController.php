<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UserModel;
use App\Utils\Validator;
use Firebase\JWT\JWT;

class AuthController
{
    private UserModel $userModel;
    private string $jwtSecret;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
        $this->jwtSecret = $_ENV['APP_SECRET'] ?? 'default_secret_key';
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = Validator::sanitizeAndValidateString($data['username'] ?? '', FILTER_SANITIZE_STRING, ['required' => true, 'max_length' => 100]);
        $password = $data['password'] ?? ''; // Password is not sanitized to preserve special characters for hashing

        if (is_null($username) || empty($password)) {
            return $this->errorResponse($response, 'Invalid username or password provided.', 400);
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return $this->errorResponse($response, 'Invalid credentials.', 401);
        }

        // JWT Payload
        $now = time();
        $payload = [
            'iss' => 'leave-management-api', // Issuer
            'aud' => 'leave-management-app', // Audience
            'iat' => $now, // Issued at
            'exp' => $now + (3600 * 24), // Expiration time (24 hours)
            'uid' => $user['id'],
            'role' => $user['role'],
            'name' => $user['employee_name']
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

        $response->getBody()->write(json_encode([
            'message' => 'Login successful',
            'token' => $jwt,
            'user' => [
                'id' => $user['id'],
                'name' => $user['employee_name'],
                'role' => $user['role']
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function errorResponse(Response $response, string $message, int $status): Response
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
