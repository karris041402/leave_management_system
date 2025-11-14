<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private string $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['APP_SECRET'] ?? 'default_secret_key';
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            return $this->errorResponse(401, 'Authorization header missing.');
        }

        list($type, $token) = explode(' ', $authHeader, 2);

        if (strtolower($type) !== 'bearer' || empty($token)) {
            return $this->errorResponse(401, 'Invalid token format.');
        }

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            
            // Add the decoded token payload to the request attributes
            $request = $request->withAttribute('token', (array) $decoded);

            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->errorResponse(401, 'Invalid or expired token: ' . $e->getMessage());
        }
    }

    private function errorResponse(int $status, string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
