<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * Auth Controller
 * 
 * Handles authentication requests.
 */
final class AuthController
{
    public function register(Request $request): Response
    {
        // TODO: Implement registration logic
        return Response::json([
            'message' => 'Registration endpoint - to be implemented',
            'status' => 'ok'
        ], 201);
    }

    public function login(Request $request): Response
    {
        // TODO: Implement login logic with JWT
        return Response::json([
            'message' => 'Login endpoint - to be implemented',
            'status' => 'ok'
        ]);
    }

    public function logout(Request $request): Response
    {
        // TODO: Implement logout logic
        return Response::json([
            'message' => 'Logout endpoint - to be implemented',
            'status' => 'ok'
        ]);
    }

    public function me(Request $request): Response
    {
        // TODO: Return current user info from JWT token
        return Response::json([
            'message' => 'Get current user endpoint - to be implemented',
            'status' => 'ok'
        ]);
    }
}

