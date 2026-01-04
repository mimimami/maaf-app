<?php

declare(strict_types=1);

namespace App\Middleware;

use MAAF\Core\Http\MiddlewareInterface;
use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * Rate Limiting Middleware
 * 
 * Limits the number of requests a client can make within a given time period.
 * This is a basic in-memory implementation and should be replaced with a persistent
 * store (e.g., Redis) for production use.
 */
final class RateLimitingMiddleware implements MiddlewareInterface
{
    /**
     * @var array<string, array{count: int, reset_time: int}>
     */
    private static array $requests = [];

    private int $maxRequests;
    private int $windowSeconds;

    public function __construct()
    {
        $this->maxRequests = (int) (getenv('RATE_LIMIT_MAX_REQUESTS') ?: 60);
        $this->windowSeconds = (int) (getenv('RATE_LIMIT_WINDOW_SECONDS') ?: 60);
    }

    public function handle(Request $request, callable $next): Response
    {
        $ip = $request->server['REMOTE_ADDR'] ?? 'unknown';
        $currentTime = time();

        if (!isset(self::$requests[$ip])) {
            self::$requests[$ip] = ['count' => 0, 'reset_time' => $currentTime + $this->windowSeconds];
        }

        // Reset if window has passed
        if ($currentTime >= self::$requests[$ip]['reset_time']) {
            self::$requests[$ip] = ['count' => 0, 'reset_time' => $currentTime + $this->windowSeconds];
        }

        if (self::$requests[$ip]['count'] >= $this->maxRequests) {
            return Response::json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
            ], 429)->withHeader('Retry-After', (string) (self::$requests[$ip]['reset_time'] - $currentTime));
        }

        // Increment request count
        self::$requests[$ip]['count']++;

        // Add rate limit headers
        $response = $next($request);
        $remaining = $this->maxRequests - self::$requests[$ip]['count'];
        
        return $response
            ->withHeader('X-RateLimit-Limit', (string) $this->maxRequests)
            ->withHeader('X-RateLimit-Remaining', (string) max(0, $remaining))
            ->withHeader('X-RateLimit-Reset', (string) self::$requests[$ip]['reset_time']);
    }
}

