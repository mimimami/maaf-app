<?php

/**
 * Rate Limiting Configuration
 */

return [
    'enabled' => getenv('RATE_LIMITING_ENABLED') !== 'false',
    'max_requests' => (int) (getenv('RATE_LIMIT_MAX_REQUESTS') ?: 60),
    'window_seconds' => (int) (getenv('RATE_LIMIT_WINDOW_SECONDS') ?: 60),
];

