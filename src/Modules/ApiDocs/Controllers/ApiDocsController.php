<?php

declare(strict_types=1);

namespace App\Modules\ApiDocs\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * API Docs Controller
 * 
 * Provides API documentation.
 */
final class ApiDocsController
{
    public function index(Request $request): Response
    {
        $docs = [
            'title' => 'MAAF API Dokumentáció',
            'version' => '1.0.0',
            'description' => 'MAAF alkalmazás API dokumentációja',
            'endpoints' => [
                [
                    'method' => 'GET',
                    'path' => '/',
                    'description' => 'Üdvözlő végpont - alkalmazás információk visszaadása',
                ],
                [
                    'method' => 'GET',
                    'path' => '/health',
                    'description' => 'Állapot ellenőrző végpont',
                ],
                [
                    'method' => 'POST',
                    'path' => '/auth/register',
                    'description' => 'Új felhasználó regisztrálása',
                ],
                [
                    'method' => 'POST',
                    'path' => '/auth/login',
                    'description' => 'Felhasználó bejelentkezése',
                ],
                [
                    'method' => 'GET',
                    'path' => '/auth/me',
                    'description' => 'Jelenlegi felhasználó információk lekérése',
                ],
                [
                    'method' => 'GET',
                    'path' => '/api-docs',
                    'description' => 'API dokumentáció (ez a végpont)',
                ],
            ],
        ];

        // Check if it's an API request
        $acceptHeader = $request->getHeader('Accept') ?? '';
        if (str_contains($acceptHeader, 'application/json')) {
            return Response::json($docs);
        }

        // Return HTML page
        return Response::html($this->getDocsHtml($docs));
    }

    private function getDocsHtml(array $docs): string
    {
        $title = $docs['title'];
        $version = $docs['version'];
        $description = $docs['description'];
        $endpoints = $docs['endpoints'] ?? [];

        $methodColors = [
            'GET' => '#10b981',
            'POST' => '#3b82f6',
            'PUT' => '#f59e0b',
            'PATCH' => '#8b5cf6',
            'DELETE' => '#ef4444',
        ];

        $html = <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #000000;
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo-image {
            max-width: 200px;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.3));
        }

        h1 {
            font-size: 48px;
            font-weight: 900;
            letter-spacing: 4px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 18px;
            color: #888;
            margin-bottom: 10px;
        }

        .version {
            color: #666;
            font-size: 14px;
        }

        .info-card {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
        }

        .info-card p {
            color: #ccc;
            line-height: 1.8;
            font-size: 16px;
        }

        .endpoints-section h2 {
            font-size: 32px;
            color: #d4af37;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .endpoint-card {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .endpoint-card:hover {
            border-color: #d4af37;
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
            transform: translateX(5px);
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .method-badge {
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1px;
            min-width: 80px;
            text-align: center;
        }

        .path {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            color: #d4af37;
            font-weight: 600;
        }

        .description {
            color: #ccc;
            font-size: 15px;
            line-height: 1.6;
            padding-left: 95px;
        }

        .back-link {
            display: inline-block;
            margin-top: 40px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            color: #000;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        }

        .back-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
        }

        .glow {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -250px;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
            z-index: -1;
        }

        @media (max-width: 768px) {
            .description {
                padding-left: 0;
                margin-top: 10px;
            }

            h1 {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="/images/logo.png" alt="MAAF Logo" class="logo-image" />
            </div>
            <h1>API Dokumentáció</h1>
            <p class="subtitle">{$description}</p>
            <div class="version">Verzió {$version}</div>
        </div>

        <div class="info-card">
            <p>Ez az oldal dokumentációt nyújt az összes elérhető API végponthoz. A metódus jelvényekkel azonosíthatod a HTTP metódust, és a végpontokra kattintva részletes információkat láthatsz.</p>
        </div>

        <div class="endpoints-section">
            <h2>Végpontok</h2>
HTML;

        foreach ($endpoints as $endpoint) {
            $method = $endpoint['method'] ?? 'GET';
            $path = $endpoint['path'] ?? '/';
            $desc = $endpoint['description'] ?? 'Nincs leírás';
            $color = $methodColors[$method] ?? '#888';
            
            $html .= <<<HTML
            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge" style="background: rgba({$this->hexToRgb($color)}, 0.2); color: {$color}; border: 1px solid {$color};">
                        {$method}
                    </span>
                    <span class="path">{$path}</span>
                </div>
                <div class="description">{$desc}</div>
            </div>
HTML;
        }

        $html .= <<<HTML
        </div>

        <div style="text-align: center;">
            <a href="/" class="back-link">← Vissza a főoldalra</a>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    private function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "{$r}, {$g}, {$b}";
    }
}

