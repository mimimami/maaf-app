<?php

/**
 * MAAF Application Configuration
 * 
 * This file configures the MAAF application.
 * It's automatically loaded by the Application class.
 */

return [
    'modules' => [
        'path' => __DIR__ . '/../src/Modules',
        'namespace' => 'App\\Modules',
    ],
    'services' => __DIR__ . '/services.php',
    'routes' => __DIR__ . '/routes.php',
    'middleware' => [
        // Middleware is loaded from config/middleware.php
        // This is just a placeholder - actual middleware is registered in public/index.php
    ],
];

