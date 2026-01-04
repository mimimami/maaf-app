<?php

declare(strict_types=1);

namespace App\Middleware;

use MAAF\Core\Http\MiddlewareInterface;
use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * CORS Middleware
 * 
 * Handles Cross-Origin Resource Sharing (CORS) headers based on configuration.
 */
final class CorsMiddleware implements MiddlewareInterface
{
    private array $config;

    public function __construct()
    {
        $configFile = __DIR__ . '/../../config/cors.php';
        $this->config = file_exists($configFile) ? require $configFile : $this->getDefaultConfig();
    }

    public function handle(Request $request, callable $next): Response
    {
        if (!$this->config['enabled']) {
            return $next($request);
        }

        $origin = $request->getHeader('Origin');
        $response = $next($request);

        if ($origin !== null && $this->isOriginAllowed($origin)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin)
                                 ->withHeader('Vary', 'Origin');

            if ($this->config['allow_credentials']) {
                $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
            }

            if ($request->getMethod() === 'OPTIONS') {
                $response = $response->withStatus(204)
                                     ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->config['allowed_methods']))
                                     ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->config['allowed_headers']))
                                     ->withHeader('Access-Control-Max-Age', (string) $this->config['max_age']);
            }

            if (!empty($this->config['exposed_headers'])) {
                $response = $response->withHeader('Access-Control-Expose-Headers', implode(', ', $this->config['exposed_headers']));
            }
        }

        return $response;
    }

    private function isOriginAllowed(string $origin): bool
    {
        if (in_array('*', $this->config['allowed_origins'], true)) {
            return true;
        }
        return in_array($origin, $this->config['allowed_origins'], true);
    }

    private function getDefaultConfig(): array
    {
        return [
            'enabled' => true,
            'allowed_origins' => ['http://localhost:5173', 'http://localhost:3000'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'exposed_headers' => [],
            'max_age' => 86400,
            'allow_credentials' => false,
        ];
    }
}

