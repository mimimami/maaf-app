<?php

declare(strict_types=1);

namespace App\Modules\Health\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * Health Controller
 * 
 * Provides health check and system information endpoints.
 */
final class HealthController
{
    public function index(Request $request): Response
    {
        return Response::json([
            'name' => 'MAAF Application',
            'version' => '1.0.0',
            'status' => 'ok',
            'timestamp' => date('c'),
            'environment' => getenv('APP_ENV') ?: 'development',
        ]);
    }

    public function health(Request $request): Response
    {
        $health = [
            'status' => 'ok',
            'timestamp' => date('c'),
            'checks' => [],
        ];

        // Database check
        try {
            $pdo = new \PDO('sqlite:' . (__DIR__ . '/../../../../database/database.sqlite'));
            $pdo->query('SELECT 1');
            $health['checks']['database'] = 'ok';
        } catch (\Exception $e) {
            $health['checks']['database'] = 'error: ' . $e->getMessage();
            $health['status'] = 'degraded';
        }

        // Check if it's an API request
        $acceptHeader = $request->getHeader('Accept') ?? '';
        if (str_contains($acceptHeader, 'application/json')) {
            return Response::json($health);
        }

        // Return HTML page
        return Response::html($this->getHealthHtml($health));
    }

    private function getHealthHtml(array $health): string
    {
        $status = $health['status'];
        $statusClass = $status === 'ok' ? 'status-ok' : 'status-error';
        $statusIcon = $status === 'ok' ? '✓' : '✗';
        $statusColor = $status === 'ok' ? '#10b981' : '#ef4444';
        
        // Státusz fordítása
        $statusTranslated = match($status) {
            'ok' => 'rendben',
            'degraded' => 'csökkentett',
            'error' => 'hiba',
            default => $status,
        };
        
        $checks = $health['checks'] ?? [];
        $timestamp = date('Y-m-d H:i:s', strtotime($health['timestamp']));

        $html = <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Állapot ellenőrzés - MAAF</title>
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
            max-width: 900px;
            width: 100%;
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
            margin-bottom: 20px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
        }

        .status-card {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 15px 40px;
            border-radius: 35px;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .status-ok {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%);
            border: 2px solid #10b981;
            color: #10b981;
        }

        .status-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.1) 100%);
            border: 2px solid #ef4444;
            color: #ef4444;
        }

        .timestamp {
            color: #888;
            font-size: 14px;
            margin-top: 15px;
        }

        .checks-section {
            margin-top: 40px;
        }

        .checks-title {
            font-size: 24px;
            color: #d4af37;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .check-item {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .check-item:hover {
            border-color: #d4af37;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        .check-name {
            font-weight: 600;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .check-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .check-ok {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid #10b981;
        }

        .check-error {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .back-link {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            color: #000;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
            letter-spacing: 1px;
        }

        .back-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        }

        .footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: #777;
            font-size: 14px;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer .company {
            color: #d4af37;
            font-weight: 600;
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
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="/images/logo.png" alt="MAAF Logo" class="logo-image" />
            </div>
            <h1>Állapot ellenőrzés</h1>
        </div>

        <div class="status-card">
            <div class="status-badge {$statusClass}">
                {$statusIcon} Rendszer {$statusTranslated}
            </div>
            <div class="timestamp">Utolsó ellenőrzés: {$timestamp}</div>
        </div>

        <div class="checks-section">
            <h2 class="checks-title">Rendszer ellenőrzések</h2>
            <div class="check-item">
                <span class="check-name">Általános állapot</span>
                <span class="check-status {$statusClass}">{$statusTranslated}</span>
            </div>
HTML;

        foreach ($checks as $checkName => $checkStatus) {
            $isOk = $checkStatus === 'ok';
            $checkClass = $isOk ? 'check-ok' : 'check-error';
            $checkDisplay = $isOk ? 'RENDBEN' : 'HIBA';
            
            // Fordítás a check nevekhez
            $checkNameTranslated = match($checkName) {
                'database' => 'Adatbázis',
                default => ucfirst($checkName),
            };
            $html .= <<<HTML
            <div class="check-item">
                <span class="check-name">{$checkNameTranslated}</span>
                <span class="check-status {$checkClass}">{$checkDisplay}</span>
            </div>
HTML;
        }

        $html .= <<<HTML
        </div>

        <div style="text-align: center; margin-top: 50px;">
            <a href="/" class="back-link">← Vissza a főoldalra</a>
        </div>

        <div class="footer">
            <p>&copy; 2024 <span class="company">Bloch Kft</span>. Minden jog fenntartva.</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }
}
