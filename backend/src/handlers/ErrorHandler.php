<?php

namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Throwable;

class ErrorHandler
{
    public function __invoke(Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response
    {
        $statusCode = 500;
        $error = 'Internal Server Error';
        $message = 'An unexpected error occurred.';

        if ($exception instanceof HttpNotFoundException) {
            $statusCode = 404;
            $error = 'Not Found';
            $message = 'The requested resource was not found.';
        } elseif ($exception instanceof HttpMethodNotAllowedException) {
            $statusCode = 405;
            $error = 'Method Not Allowed';
            $message = 'The request method is not supported for the requested resource.';
        } elseif ($exception instanceof \PDOException) {
            $statusCode = 500;
            $error = 'Database Error';
            $message = 'A database error occurred.';
        }

        // Log the error
        if ($logErrors) {
            $logMessage = sprintf(
                "[%s] %s: %s in %s on line %d",
                date('Y-m-d H:i:s'),
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            error_log($logMessage);
        }

        // Prepare response body
        $responseBody = [
            'error' => $error,
            'message' => $message,
        ];

        if ($displayErrorDetails) {
            $responseBody['details'] = [
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode($responseBody));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
