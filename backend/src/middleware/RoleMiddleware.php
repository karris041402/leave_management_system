<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getAttribute('token');

        if (!$token || !isset($token['role'])) {
            // This should ideally be caught by AuthMiddleware, but as a safeguard:
            return $this->errorResponse(401, 'Authentication required.');
        }

        if (!in_array($token['role'], $this->allowedRoles)) {
            return $this->errorResponse(403, 'Access denied. Insufficient role.');
        }

        return $handler->handle($request);
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
