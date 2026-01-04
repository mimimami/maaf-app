<?php

declare(strict_types=1);

namespace App\Modules\Docs;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        // No services needed
    }

    public static function registerRoutes(Router $router): void
    {
        $router->addRoute('GET', '/docs', [
            \App\Modules\Docs\Controllers\DocsController::class,
            'index'
        ]);
        
        $router->addRoute('GET', '/docs/{slug}', [
            \App\Modules\Docs\Controllers\DocsController::class,
            'show'
        ]);
    }
}

