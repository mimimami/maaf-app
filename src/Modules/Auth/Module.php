<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

/**
 * Auth Module
 * 
 * Provides authentication endpoints (login, register, etc.).
 */
final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        // Register auth services
    }

    public static function registerRoutes(Router $router): void
    {
        $router->addRoute('POST', '/auth/register', [
            \App\Modules\Auth\Controllers\AuthController::class,
            'register'
        ]);
        
        $router->addRoute('POST', '/auth/login', [
            \App\Modules\Auth\Controllers\AuthController::class,
            'login'
        ]);
        
        $router->addRoute('POST', '/auth/logout', [
            \App\Modules\Auth\Controllers\AuthController::class,
            'logout'
        ]);
        
        $router->addRoute('GET', '/auth/me', [
            \App\Modules\Auth\Controllers\AuthController::class,
            'me'
        ]);
    }
}

