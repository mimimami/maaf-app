<?php

declare(strict_types=1);

namespace App\Exceptions;

use MAAF\Core\Http\Response;

/**
 * Exception Handler
 * 
 * Handles exceptions and converts them to HTTP responses.
 */
final class Handler
{
    private bool $debug;

    public function __construct()
    {
        $this->debug = getenv('APP_DEBUG') === 'true';
    }

    public function handle(\Throwable $exception): Response
    {
        // Log the exception
        $this->logException($exception);

        // Convert to HTTP response
        return $this->renderException($exception);
    }

    private function renderException(\Throwable $exception): Response
    {
        $statusCode = $this->getStatusCode($exception);
        $message = $this->getMessage($exception);

        $data = [
            'error' => $this->getErrorType($exception),
            'message' => $message,
        ];

        if ($this->debug) {
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
            $data['trace'] = $exception->getTraceAsString();
        }

        return Response::json($data, $statusCode);
    }

    private function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof \App\Core\Http\UnauthorizedException) {
            return 401;
        }

        if ($exception instanceof \App\Core\Http\ForbiddenException) {
            return 403;
        }

        if ($exception instanceof \App\Core\Http\NotFoundException) {
            return 404;
        }

        return 500;
    }

    private function getMessage(\Throwable $exception): string
    {
        if (!$this->debug && $exception->getCode() === 500) {
            return 'Internal Server Error';
        }

        return $exception->getMessage() ?: 'An error occurred';
    }

    private function getErrorType(\Throwable $exception): string
    {
        $class = get_class($exception);
        $parts = explode('\\', $class);
        return end($parts);
    }

    private function logException(\Throwable $exception): void
    {
        $logPath = __DIR__ . '/../../storage/logs/error.log';
        $logDir = dirname($logPath);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logEntry = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

