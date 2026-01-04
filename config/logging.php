<?php

/**
 * Logging Configuration
 * 
 * Monolog logging configuration.
 */

return [
    'default' => 'file',
    
    'channels' => [
        'file' => [
            'driver' => 'single',
            'path' => __DIR__ . '/../storage/logs/app.log',
            'level' => getenv('LOG_LEVEL') ?: 'debug',
        ],
        
        'daily' => [
            'driver' => 'daily',
            'path' => __DIR__ . '/../storage/logs/app.log',
            'level' => getenv('LOG_LEVEL') ?: 'debug',
            'days' => 14,
        ],
        
        'error' => [
            'driver' => 'single',
            'path' => __DIR__ . '/../storage/logs/error.log',
            'level' => 'error',
        ],
    ],
];

