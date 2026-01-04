# MAAF Példák és Use Cases

Ez a dokumentum gyakorlati példákat tartalmaz a MAAF framework használatához.

## Tartalomjegyzék

1. [CRUD Modul](#crud-modul)
2. [Autentikáció](#autentikáció)
3. [File Upload](#file-upload)
4. [Email Küldés](#email-küldés)
5. [API Pagination](#api-pagination)

---

## CRUD Modul

### Teljes CRUD Modul Példa

```php
<?php
// src/Modules/Product/Module.php
namespace App\Modules\Product;

use DI\ContainerBuilder;
use MAAF\Core\Routing\Router;

final class Module
{
    public static function registerServices(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            \App\Modules\Product\Services\ProductService::class => 
                DI\create(\App\Modules\Product\Services\ProductService::class),
        ]);
    }

    public static function registerRoutes(Router $router): void
    {
        $router->addRoute('GET', '/products', [
            \App\Modules\Product\Controllers\ProductController::class,
            'index'
        ]);
        
        $router->addRoute('GET', '/products/{id}', [
            \App\Modules\Product\Controllers\ProductController::class,
            'show'
        ]);
        
        $router->addRoute('POST', '/products', [
            \App\Modules\Product\Controllers\ProductController::class,
            'create'
        ]);
        
        $router->addRoute('PUT', '/products/{id}', [
            \App\Modules\Product\Controllers\ProductController::class,
            'update'
        ]);
        
        $router->addRoute('DELETE', '/products/{id}', [
            \App\Modules\Product\Controllers\ProductController::class,
            'delete'
        ]);
    }
}
```

```php
<?php
// src/Modules/Product/Controllers/ProductController.php
namespace App\Modules\Product\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;
use App\Modules\Product\Services\ProductService;

final class ProductController
{
    public function __construct(
        private ProductService $service
    ) {
    }

    public function index(Request $request): Response
    {
        $products = $this->service->getAll();
        return Response::json(['products' => $products]);
    }

    public function show(Request $request, string $id): Response
    {
        $product = $this->service->getById((int)$id);
        
        if (!$product) {
            return Response::json(['error' => 'Product not found'], 404);
        }

        return Response::json(['product' => $product]);
    }

    public function create(Request $request): Response
    {
        $data = $request->getBody();
        $product = $this->service->create($data);
        return Response::json(['product' => $product], 201);
    }

    public function update(Request $request, string $id): Response
    {
        $data = $request->getBody();
        $product = $this->service->update((int)$id, $data);
        
        if (!$product) {
            return Response::json(['error' => 'Product not found'], 404);
        }

        return Response::json(['product' => $product]);
    }

    public function delete(Request $request, string $id): Response
    {
        $deleted = $this->service->delete((int)$id);
        
        if (!$deleted) {
            return Response::json(['error' => 'Product not found'], 404);
        }

        return Response::empty(204);
    }
}
```

---

## Autentikáció

### Login Endpoint

```php
<?php
// src/Modules/Auth/Controllers/AuthController.php
namespace App\Modules\Auth\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;
use App\Modules\Auth\Services\AuthService;

final class AuthController
{
    public function __construct(
        private AuthService $authService
    ) {
    }

    public function login(Request $request): Response
    {
        $data = $request->getBody();
        
        // Validation
        if (empty($data['email']) || empty($data['password'])) {
            return Response::json(['error' => 'Email and password required'], 400);
        }

        try {
            $result = $this->authService->login($data['email'], $data['password']);
            return Response::json($result);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function me(Request $request): Response
    {
        $token = $request->getHeader('Authorization');
        
        if (!$token) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $user = $this->authService->getUserFromToken($token);
        
        return Response::json(['user' => $user]);
    }
}
```

---

## File Upload

### File Upload Handler

```php
<?php
namespace App\Modules\File\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

final class FileController
{
    public function upload(Request $request): Response
    {
        if (!isset($_FILES['file'])) {
            return Response::json(['error' => 'No file uploaded'], 400);
        }

        $file = $_FILES['file'];
        
        // Validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return Response::json(['error' => 'Invalid file type'], 400);
        }

        // Move uploaded file
        $uploadDir = __DIR__ . '/../../../../storage/uploads/';
        $filename = uniqid() . '_' . $file['name'];
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return Response::json(['error' => 'Upload failed'], 500);
        }

        return Response::json([
            'success' => true,
            'filename' => $filename,
            'url' => '/uploads/' . $filename
        ], 201);
    }
}
```

---

## Email Küldés

### Email Service

```php
<?php
namespace App\Modules\Email\Services;

final class EmailService
{
    public function send(string $to, string $subject, string $body): bool
    {
        $headers = [
            'From: noreply@example.com',
            'Reply-To: noreply@example.com',
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
        ];

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
}
```

---

## API Pagination

### Paginated Response

```php
<?php
namespace App\Modules\Product\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

final class ProductController
{
    public function index(Request $request): Response
    {
        $page = (int)($request->getQuery('page', 1));
        $perPage = (int)($request->getQuery('per_page', 20));
        $offset = ($page - 1) * $perPage;

        $products = $this->service->getPaginated($offset, $perPage);
        $total = $this->service->getTotalCount();

        return Response::json([
            'data' => $products,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ]);
    }
}
```

---

## További Példák

- [MAAF Core Examples](https://github.com/mimimami/maaf-core/tree/main/docs)
- [SmartLearning Implementation](../smartlearning/src/Modules/) - Teljes alkalmazás példa

