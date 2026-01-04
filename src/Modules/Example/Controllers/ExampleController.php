<?php

declare(strict_types=1);

namespace App\Modules\Example\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

final class ExampleController
{
    public function index(Request $request): Response
    {
        return Response::json([
            'message' => 'Welcome to MAAF!',
            'version' => '1.0.0',
            'status' => 'success'
        ]);
    }
}

