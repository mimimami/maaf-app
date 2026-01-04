<?php

declare(strict_types=1);

namespace App\Modules\Welcome;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

/**
 * Welcome Module
 * 
 * Provides a beautiful welcome page for the application.
 */
final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        // No additional services needed
    }

    public static function registerRoutes(Router $router): void
    {
        $router->addRoute('GET', '/', [
            \App\Modules\Welcome\Controllers\WelcomeController::class,
            'index'
        ]);
    }
}

