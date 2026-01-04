<?php

declare(strict_types=1);

namespace App\Modules\Example;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        // Register services
    }

    public static function registerRoutes(Router $router): void
    {
        $router->addRoute('GET', '/example', [
            \App\Modules\Example\Controllers\ExampleController::class,
            'index'
        ]);
    }
}

