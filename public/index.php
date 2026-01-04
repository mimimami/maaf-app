<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MAAF\Core\Application;
use App\Middleware\CorsMiddleware;
use App\Middleware\LoggingMiddleware;
use App\Exceptions\Handler;

// Set error handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

// Set exception handler
set_exception_handler(function (\Throwable $exception) {
    $handler = new Handler();
    $response = $handler->handle($exception);
    $response->send();
    exit(1);
});

$app = new Application(__DIR__ . '/..');

// Load middleware configuration
$middlewareConfigFile = __DIR__ . '/../config/middleware.php';
if (file_exists($middlewareConfigFile)) {
    $middlewareConfig = require $middlewareConfigFile;
    $middlewares = $middlewareConfig['middleware'] ?? [];
    
    foreach ($middlewares as $middlewareClass) {
        if (class_exists($middlewareClass)) {
            $app->addMiddleware(new $middlewareClass());
        }
    }
} else {
    // Fallback: Add default middleware
    $corsConfigFile = __DIR__ . '/../config/cors.php';
    if (file_exists($corsConfigFile)) {
        $corsConfig = require $corsConfigFile;
        if ($corsConfig['enabled'] ?? true) {
            $app->addMiddleware(new CorsMiddleware());
        }
    }
    
    // Add logging middleware if enabled
    if (getenv('LOG_REQUESTS') !== 'false') {
        $app->addMiddleware(new LoggingMiddleware());
    }
}

$app->run();
