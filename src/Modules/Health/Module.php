<?php

declare(strict_types=1);

namespace App\Modules\Health;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

/**
 * Health Module
 * 
 * Provides health check and system information endpoints.
 */
final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        // No additional services needed
    }

    public static function registerRoutes(Router $router): void
    {
        // Root route moved to Welcome module
        $router->addRoute('GET', '/health', [
            \App\Modules\Health\Controllers\HealthController::class,
            'health'
        ]);
    }
}
