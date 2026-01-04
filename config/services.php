<?php

use function DI\factory;

return [
    // DI szolgáltatások regisztrálása
    // A kontrollerek automatikusan példányosítva lesznek autowiring-gel
    
    // CORS Middleware
    \App\Middleware\CorsMiddleware::class => DI\create(\App\Middleware\CorsMiddleware::class),
    
    // Logging Middleware
    \App\Middleware\LoggingMiddleware::class => DI\create(\App\Middleware\LoggingMiddleware::class),
    
    // Rate Limiting Middleware
    \App\Middleware\RateLimitingMiddleware::class => DI\create(\App\Middleware\RateLimitingMiddleware::class),
    
    // CORS Configuration
    'cors.config' => DI\factory(function () {
        return require __DIR__ . '/cors.php';
    }),
];

