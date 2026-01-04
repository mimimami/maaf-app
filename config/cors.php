<?php

/**
 * CORS Configuration
 * 
 * Cross-Origin Resource Sharing (CORS) configuration.
 */

return [
    'enabled' => getenv('CORS_ENABLED') !== 'false',
    
    'allowed_origins' => explode(',', getenv('CORS_ALLOWED_ORIGINS') ?: 'http://localhost:5173,http://localhost:3000'),
    
    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],
    
    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
    ],
    
    'exposed_headers' => [],
    
    'max_age' => (int) (getenv('CORS_MAX_AGE') ?: 86400), // 24 hours
    
    'allow_credentials' => getenv('CORS_ALLOW_CREDENTIALS') === 'true',
];
