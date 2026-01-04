<?php

declare(strict_types=1);

namespace App\Modules\Welcome\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * Welcome Controller
 * 
 * Displays a beautiful welcome page.
 */
final class WelcomeController
{
    public function index(Request $request): Response
    {
        // Check if it's an API request (Accept: application/json header)
        $acceptHeader = $request->getHeader('Accept') ?? '';
        
        if (str_contains($acceptHeader, 'application/json')) {
            // Return JSON for API requests
            return Response::json([
                'name' => 'MAAF Application',
                'version' => '1.0.0',
                'status' => 'ok',
                'timestamp' => date('c'),
                'environment' => getenv('APP_ENV') ?: 'development',
                'php_version' => PHP_VERSION,
                'framework' => 'MAAF Core',
                'framework_version' => '1.0.0',
            ]);
        }

        // Return HTML welcome page
        $html = $this->getWelcomeHtml();
        return Response::html($html);
    }

    private function getWelcomeHtml(): string
    {
        $environment = getenv('APP_ENV') ?: 'development';
        $phpVersion = PHP_VERSION;
        $appName = 'MAAF Application';
        $appVersion = '1.0.0';

        return <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$appName}</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #fff;
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.8s ease-in;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            margin-bottom: 50px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            animation: logoFadeIn 1s ease-in;
        }

        @keyframes logoFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .logo-image {
            max-width: 400px;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.3));
            animation: logoGlow 3s ease-in-out infinite;
        }

        @keyframes logoGlow {
            0%, 100% {
                filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.3));
            }
            50% {
                filter: drop-shadow(0 0 30px rgba(255, 215, 0, 0.5));
            }
        }

        .subtitle {
            font-size: 20px;
            color: #888;
            margin-bottom: 20px;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .version {
            color: #666;
            font-size: 16px;
            margin-bottom: 40px;
            letter-spacing: 1px;
        }

        .status-badge {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(255, 215, 0, 0.1) 100%);
            color: #ffd700;
            border: 2px solid #d4af37;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 50px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            letter-spacing: 1px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 50px 0;
        }

        .info-card {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            padding: 35px 25px;
            border-radius: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .info-card:hover {
            transform: translateY(-8px);
            border-color: #d4af37;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(255, 215, 0, 0.1) 100%);
        }

        .info-card h3 {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .info-card p {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
        }

        .links {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 60px;
        }

        .link {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            color: #000;
            text-decoration: none;
            border-radius: 35px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.4);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .link:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.6);
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        }

        .link.secondary {
            background: transparent;
            color: #d4af37;
            border: 2px solid #d4af37;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }

        .link.secondary:hover {
            background: rgba(212, 175, 55, 0.1);
            box-shadow: 0 0 25px rgba(212, 175, 55, 0.5);
        }

        .link.outline {
            background: transparent;
            color: #888;
            border: 2px solid #444;
            box-shadow: none;
        }

        .link.outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: #666;
            color: #fff;
        }

        .footer {
            margin-top: 80px;
            padding-top: 40px;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            color: #666;
            font-size: 14px;
        }

        .footer p {
            margin: 10px 0;
            letter-spacing: 1px;
        }

        .footer .heart {
            color: #d4af37;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        .footer .company {
            color: #d4af37;
            font-weight: 600;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* Decorative elements */
        .glow {
            position: absolute;
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
            h1 {
                font-size: 48px;
                letter-spacing: 4px;
            }

            .company-name {
                font-size: 18px;
                letter-spacing: 2px;
            }

            .subtitle {
                font-size: 16px;
            }

            .logo-image {
                max-width: 300px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .link {
                width: 100%;
                padding: 14px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="container">
        <div class="logo-section">
            <div class="logo-container">
                <img src="/images/logo.png" alt="MAAF Logo" class="logo-image" />
            </div>
            <p class="subtitle">Modular Application Architecture Framework</p>
            <div class="version">v{$appVersion}</div>
        </div>
        
        <div class="status-badge">✓ Alkalmazás fut</div>

        <div class="info-grid">
            <div class="info-card">
                <h3>Környezet</h3>
                <p>{$environment}</p>
            </div>
            <div class="info-card">
                <h3>PHP Verzió</h3>
                <p>{$phpVersion}</p>
            </div>
            <div class="info-card">
                <h3>Állapot</h3>
                <p>Kész</p>
            </div>
        </div>

        <div class="links">
            <a href="/health" class="link">Állapot ellenőrzés</a>
            <a href="/api-docs" class="link secondary">API Dokumentáció</a>
            <a href="/docs" class="link secondary">Dokumentáció</a>
        </div>

        <div class="footer">
            <p>MAAF Framework felhasználásával készült <span class="heart">❤️</span></p>
            <p style="margin-top: 10px; font-size: 12px; color: #555;">Készen állsz valami fantasztikus építésére?</p>
            <p style="margin-top: 20px; font-size: 13px; color: #777;">&copy; 2024 <span class="company">Bloch Kft</span>. Minden jog fenntartva.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}

