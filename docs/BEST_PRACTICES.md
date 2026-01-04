# MAAF Best Practices

Ez a dokumentum összefoglalja a MAAF framework használatának ajánlott gyakorlatait.

## Tartalomjegyzék

1. [Kód Szervezés](#kód-szervezés)
2. [Modul Fejlesztés](#modul-fejlesztés)
3. [Security](#security)
4. [Performance](#performance)
5. [Testing](#testing)
6. [Error Handling](#error-handling)

---

## Kód Szervezés

### Modul Struktúra

```
src/Modules/
└── YourModule/
    ├── Module.php              # Modul regisztráció
    ├── Controllers/            # HTTP kontrollerek
    │   └── YourController.php
    ├── Services/               # Business logic
    │   └── YourService.php
    ├── Repositories/           # Data access
    │   └── YourRepository.php
    ├── Models/                 # Domain models
    │   └── YourModel.php
    └── DTOs/                   # Data Transfer Objects
        └── YourDTO.php
```

### Naming Conventions

- **Modulok**: PascalCase (pl. `UserManagement`)
- **Kontrollerek**: PascalCase + `Controller` suffix (pl. `UserController`)
- **Services**: PascalCase + `Service` suffix (pl. `UserService`)
- **Models**: PascalCase (pl. `User`)
- **Routes**: kebab-case (pl. `/user-management`)

---

## Modul Fejlesztés

### 1. Modul Létrehozása

```bash
php maaf make:module UserManagement
```

Ez létrehozza a modul struktúrát automatikusan.

### 2. Module.php Regisztráció

```php
<?php

namespace App\Modules\UserManagement;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        // Service regisztrációk
        $builder->addDefinitions([
            \App\Modules\UserManagement\Services\UserService::class => 
                DI\create(\App\Modules\UserManagement\Services\UserService::class),
        ]);
    }

    public static function registerRoutes(Router $router): void
    {
        $router->addRoute('GET', '/users', [
            \App\Modules\UserManagement\Controllers\UserController::class,
            'index'
        ]);
        
        $router->addRoute('POST', '/users', [
            \App\Modules\UserManagement\Controllers\UserController::class,
            'create'
        ]);
    }
}
```

### 3. Controller Best Practices

```php
<?php

namespace App\Modules\UserManagement\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;
use App\Modules\UserManagement\Services\UserService;

final class UserController
{
    public function __construct(
        private UserService $userService
    ) {
    }

    public function index(Request $request): Response
    {
        $users = $this->userService->getAllUsers();
        return Response::json(['users' => $users]);
    }

    public function create(Request $request): Response
    {
        $data = $request->getBody();
        
        // Validation
        if (empty($data['email'])) {
            return Response::json(['error' => 'Email is required'], 400);
        }

        $user = $this->userService->createUser($data);
        return Response::json(['user' => $user], 201);
    }
}
```

---

## Security

### 1. Input Validation

```php
// Mindig validáld a bemeneti adatokat
public function create(Request $request): Response
{
    $data = $request->getBody();
    
    // Email validation
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return Response::json(['error' => 'Invalid email'], 400);
    }
    
    // Password strength
    if (strlen($data['password']) < 8) {
        return Response::json(['error' => 'Password too short'], 400);
    }
}
```

### 2. SQL Injection Prevention

```php
// MINDIG használj prepared statements-t
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
```

### 3. XSS Prevention

```php
// HTML escape minden user input-ot
$safeOutput = htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

### 4. CORS Beállítás

```php
// config/cors.php
return [
    'enabled' => true,
    'allowed_origins' => [
        'https://your-production-domain.com',
        // NE használj '*' production-ben!
    ],
];
```

### 5. JWT Secret

```php
// Mindig erős, random secret-et használj
// Minimum 32 karakter
JWT_SECRET=your-very-long-random-secret-key-minimum-32-characters-long
```

---

## Performance

### 1. Database Optimizáció

```php
// Használj indexeket
CREATE INDEX idx_user_email ON users(email);

// Limit és offset használata nagy adathalmazoknál
SELECT * FROM users LIMIT 20 OFFSET 0;
```

### 2. Caching

```php
// Implementálj caching-et gyakran használt adatokhoz
class UserService
{
    private array $cache = [];

    public function getUser(int $id): array
    {
        if (isset($this->cache[$id])) {
            return $this->cache[$id];
        }

        $user = $this->repository->find($id);
        $this->cache[$id] = $user;
        return $user;
    }
}
```

### 3. Eager Loading

```php
// Kerüld a N+1 query problémát
// Rossz:
foreach ($users as $user) {
    $posts = $this->getPosts($user->id); // N+1 query
}

// Jó:
$usersWithPosts = $this->getUsersWithPosts(); // 1 query with JOIN
```

### 4. Composer Optimize

```bash
# Production build
composer install --optimize-autoloader --no-dev
```

---

## Testing

### 1. Unit Tesztek

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Modules\UserManagement\Services\UserService;

class UserServiceTest extends TestCase
{
    public function testCreateUser(): void
    {
        $service = new UserService(/* mock repository */);
        $user = $service->createUser([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertNotNull($user);
        $this->assertEquals('test@example.com', $user['email']);
    }
}
```

### 2. Feature Tesztek

```php
<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class UserApiTest extends TestCase
{
    public function testGetUsers(): void
    {
        $response = $this->get('/users');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
```

### 3. Teszt Futtatása

```bash
# Összes teszt
composer test

# Vagy PHPUnit direktben
vendor/bin/phpunit
```

---

## Error Handling

### 1. Exception Handler Használata

A projekt tartalmaz egy `App\Exceptions\Handler` osztályt, amely automatikusan kezeli a hibákat.

### 2. Custom Exceptions

```php
<?php

namespace App\Modules\UserManagement\Exceptions;

class UserNotFoundException extends \Exception
{
    public function __construct(int $userId)
    {
        parent::__construct("User with ID {$userId} not found", 404);
    }
}
```

### 3. Error Logging

```php
// Automatikusan logolva a storage/logs/error.log fájlba
throw new \Exception('Something went wrong');
```

---

## Code Style

### PHP-CS-Fixer

```bash
# Check code style
composer lint

# Fix code style
composer fix
```

### PHPStan

```bash
# Static analysis
vendor/bin/phpstan analyse src
```

---

## További Ajánlások

1. **Version Control**: Mindig használj Git-et
2. **Code Reviews**: Minden változtatást review-zolj
3. **Documentation**: Dokumentáld a komplex logikát
4. **DRY Principle**: Ne ismételd a kódot
5. **SOLID Principles**: Kövesd a SOLID elveket

---

## További Források

- [PHP The Right Way](https://phptherightway.com)
- [PSR Standards](https://www.php-fig.org/psr/)
- [MAAF Core Dokumentáció](https://github.com/mimimami/maaf-core)

