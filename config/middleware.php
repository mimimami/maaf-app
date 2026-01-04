<?php

/**
 * Middleware Configuration
 * 
 * Defines the middleware pipeline order.
 */

return [
    // Middleware execution order (first to last)
    'middleware' => [
        \App\Middleware\CorsMiddleware::class,
        \App\Middleware\LoggingMiddleware::class,
        // Add more middleware here
    ],
    
    // Route-specific middleware (optional)
    'route_middleware' => [
        // 'auth' => \App\Middleware\AuthMiddleware::class,
        // 'rate_limit' => \App\Middleware\RateLimitingMiddleware::class,
    ],
];

