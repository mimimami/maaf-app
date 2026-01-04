<?php

declare(strict_types=1);

namespace App\Modules\ApiDocs;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

/**
 * API Docs Module
 * 
 * Provides API documentation endpoint.
 */
final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        // No additional services needed
    }

    public static function registerRoutes(Router $router): void
    {
        $router->addRoute('GET', '/api-docs', [
            \App\Modules\ApiDocs\Controllers\ApiDocsController::class,
            'index'
        ]);
    }
}

