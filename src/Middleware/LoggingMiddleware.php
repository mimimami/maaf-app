<?php

declare(strict_types=1);

namespace App\Middleware;

use MAAF\Core\Http\MiddlewareInterface;
use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * Logging Middleware
 * 
 * Logs incoming requests and outgoing responses.
 */
final class LoggingMiddleware implements MiddlewareInterface
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = __DIR__ . '/../../storage/logs/app.log';
        
        // Ensure log directory exists
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public function handle(Request $request, callable $next): Response
    {
        $startTime = microtime(true);
        
        // Log request
        $this->log(sprintf(
            '[%s] %s %s',
            date('Y-m-d H:i:s'),
            $request->getMethod(),
            $request->getPath()
        ));

        // Process request
        $response = $next($request);

        // Calculate duration
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Log response
        $this->log(sprintf(
            '[%s] Response Status: %d | Duration: %sms',
            date('Y-m-d H:i:s'),
            $response->getStatusCode(),
            $duration
        ));

        return $response;
    }

    private function log(string $message): void
    {
        $logEntry = $message . PHP_EOL;
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

