<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

// ---------------------------
// Load Environment Variables
// ---------------------------
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// ---------------------------
// Set up PHP-DI Container
// ---------------------------
$container = new Container();
require __DIR__ . '/../src/config/dependencies.php';

// ---------------------------
// Create Slim App
// ---------------------------
AppFactory::setContainer($container);
$app = AppFactory::create();

// ---------------------------
// MIDDLEWARE ORDER (IMPORTANT!)
// ---------------------------
$app->add(new \App\Middleware\CorsMiddleware()); // 1. CORS first

$displayErrorDetails = $_ENV['APP_ENV'] === 'development';
$logErrors = true;
$logErrorDetails = true;
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler(new \App\Handlers\ErrorHandler());

$app->addRoutingMiddleware(); // 3. Routing last

// ---------------------------
// CATCH ALL OPTIONS REQUESTS
// ---------------------------
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withStatus(200);
});

// ---------------------------
// Register Routes
// ---------------------------
require __DIR__ . '/../src/routes/api.php';

// ---------------------------
// Run the App
// ---------------------------
$app->addBodyParsingMiddleware();
$app->run();
