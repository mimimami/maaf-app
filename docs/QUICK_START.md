# MAAF Quick Start Guide

Gyors útmutató a MAAF framework használatához.

## 1. Projekt Létrehozása

```bash
composer create-project maaf/app my-app
cd my-app
```

## 2. Konfiguráció

Az installer automatikusan kérdez, de manuálisan is beállíthatod:

```bash
# .env fájl szerkesztése
nano .env
```

## 3. Adatbázis Beállítása

```bash
# Migrációk futtatása
php maaf migrate

# Seed adatok (opcionális)
php maaf seed
```

## 4. Szerver Indítása

```bash
php maaf serve
```

Nyisd meg: http://localhost:8000

## 5. Első Modul Létrehozása

```bash
php maaf make:module MyModule
```

Ez létrehozza a modul struktúrát a `src/Modules/MyModule/` könyvtárban.

## 6. Route Hozzáadása

Szerkeszd a `src/Modules/MyModule/Module.php` fájlt:

```php
public static function registerRoutes(Router $router): void
{
    $router->addRoute('GET', '/my-route', [
        \App\Modules\MyModule\Controllers\MyModuleController::class,
        'index'
    ]);
}
```

## 7. Controller Írása

```php
<?php

namespace App\Modules\MyModule\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

final class MyModuleController
{
    public function index(Request $request): Response
    {
        return Response::json(['message' => 'Hello from MyModule!']);
    }
}
```

## További Lépések

- [Frontend Integráció](FRONTEND_INTEGRATION.md)
- [Deployment](DEPLOYMENT.md)
- [Best Practices](BEST_PRACTICES.md)

