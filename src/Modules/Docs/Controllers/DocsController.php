<?php

declare(strict_types=1);

namespace App\Modules\Docs\Controllers;

use MAAF\Core\Http\Request;
use MAAF\Core\Http\Response;

/**
 * Docs Controller
 * 
 * Displays documentation pages.
 */
final class DocsController
{
    private const DOCS_DIR = __DIR__ . '/../../../../docs';
    
    private const DOCS_STRUCTURE = [
        'kezdoknek' => [
            'title' => 'Kezdőknek',
            'icon' => '🚀',
            'docs' => [
                'quick-start' => [
                    'file' => 'QUICK_START.md',
                    'title' => 'Gyors Kezdés',
                    'description' => 'Gyors útmutató 5 perc alatt',
                ],
            ],
        ],
        'fejlesztes' => [
            'title' => 'Fejlesztés',
            'icon' => '💻',
            'docs' => [
                'frontend-integration' => [
                    'file' => 'FRONTEND_INTEGRATION.md',
                    'title' => 'Frontend Integráció',
                    'description' => 'React, Vue, Vanilla JS integráció',
                ],
                'best-practices' => [
                    'file' => 'BEST_PRACTICES.md',
                    'title' => 'Best Practices',
                    'description' => 'Ajánlott fejlesztési gyakorlatok',
                ],
                'cli-commands' => [
                    'file' => 'CLI_COMMANDS.md',
                    'title' => 'CLI Parancsok',
                    'description' => 'MAAF CLI tool használata',
                ],
                'examples' => [
                    'file' => 'EXAMPLES.md',
                    'title' => 'Példák és Use Cases',
                    'description' => 'Gyakorlati példák',
                ],
            ],
        ],
        'deployment' => [
            'title' => 'Deployment',
            'icon' => '🚀',
            'docs' => [
                'deployment' => [
                    'file' => 'DEPLOYMENT.md',
                    'title' => 'Deployment Útmutató',
                    'description' => 'Docker, VPS, Cloud deploy',
                ],
                'github-actions' => [
                    'file' => 'GITHUB_ACTIONS.md',
                    'title' => 'GitHub Actions CI/CD',
                    'description' => 'Automatizált tesztelés és deploy',
                ],
            ],
        ],
    ];

    public function index(Request $request): Response
    {
        $acceptHeader = $request->getHeader('Accept') ?? '';
        if (str_contains($acceptHeader, 'application/json')) {
            return Response::json([
                'structure' => self::DOCS_STRUCTURE,
            ]);
        }

        return Response::html($this->getIndexHtml());
    }

    public function show(Request $request, string $slug): Response
    {
        $doc = $this->findDoc($slug);
        
        if (!$doc) {
            return Response::html($this->getNotFoundHtml($slug), 404);
        }

        $filePath = self::DOCS_DIR . '/' . $doc['file'];
        
        if (!file_exists($filePath)) {
            return Response::html($this->getNotFoundHtml($slug), 404);
        }

        $content = file_get_contents($filePath);
        $html = $this->markdownToHtml($content);

        $acceptHeader = $request->getHeader('Accept') ?? '';
        if (str_contains($acceptHeader, 'application/json')) {
            return Response::json([
                'slug' => $slug,
                'title' => $doc['title'],
                'content' => $html,
            ]);
        }

        return Response::html($this->getDocHtml($doc['title'], $html, $slug));
    }

    private function findDoc(string $slug): ?array
    {
        foreach (self::DOCS_STRUCTURE as $category) {
            foreach ($category['docs'] as $docSlug => $doc) {
                if ($docSlug === $slug) {
                    return $doc;
                }
            }
        }
        return null;
    }

    private function getIndexHtml(): string
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentáció - MAAF</title>
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

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .category {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 15px;
            padding: 30px;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .category-icon {
            font-size: 32px;
        }

        .category-title {
            font-size: 24px;
            color: #d4af37;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .docs-list {
            list-style: none;
        }

        .doc-item {
            margin-bottom: 15px;
        }

        .doc-link {
            display: block;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .doc-link:hover {
            background: rgba(212, 175, 55, 0.1);
            border-color: #d4af37;
            transform: translateX(5px);
        }

        .doc-title {
            font-size: 18px;
            font-weight: 600;
            color: #d4af37;
            margin-bottom: 5px;
        }

        .doc-description {
            font-size: 14px;
            color: #aaa;
        }

        .back-link {
            display: inline-block;
            margin-top: 50px;
            padding: 14px 35px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            color: #000;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
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
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="/images/logo.png" alt="MAAF Logo" class="logo-image" />
            </div>
            <h1>Dokumentáció</h1>
            <p class="subtitle">Teljes útmutató a MAAF framework használatához</p>
        </div>

        <div class="categories">
HTML;

        foreach (self::DOCS_STRUCTURE as $categoryKey => $category) {
            $html .= <<<HTML
            <div class="category">
                <div class="category-header">
                    <span class="category-icon">{$category['icon']}</span>
                    <h2 class="category-title">{$category['title']}</h2>
                </div>
                <ul class="docs-list">
HTML;

            foreach ($category['docs'] as $slug => $doc) {
                $html .= <<<HTML
                    <li class="doc-item">
                        <a href="/docs/{$slug}" class="doc-link">
                            <div class="doc-title">{$doc['title']}</div>
                            <div class="doc-description">{$doc['description']}</div>
                        </a>
                    </li>
HTML;
            }

            $html .= <<<HTML
                </ul>
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

    private function getDocHtml(string $title, string $content, string $slug): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - MAAF Dokumentáció</title>
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
            max-width: 1000px;
            margin: 0 auto;
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-image {
            max-width: 150px;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.3));
        }

        h1 {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: 3px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .content {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, rgba(255, 215, 0, 0.02) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 15px;
            padding: 40px;
            line-height: 1.8;
        }

        .content h1, .content h2, .content h3 {
            color: #d4af37;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .content h1 {
            font-size: 32px;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            padding-bottom: 10px;
        }

        .content h2 {
            font-size: 26px;
        }

        .content h3 {
            font-size: 20px;
        }

        .content p {
            margin-bottom: 15px;
            color: #ccc;
        }

        .content ul, .content ol {
            margin-left: 30px;
            margin-bottom: 15px;
            color: #ccc;
        }

        .content li {
            margin-bottom: 8px;
        }

        .content code {
            background: rgba(212, 175, 55, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #ffd700;
        }

        .content pre {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 8px;
            padding: 20px;
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .content pre code {
            background: none;
            padding: 0;
            color: #fff;
        }

        .content a {
            color: #d4af37;
            text-decoration: none;
            border-bottom: 1px solid rgba(212, 175, 55, 0.5);
        }

        .content a:hover {
            color: #ffd700;
            border-bottom-color: #ffd700;
        }

        .content blockquote {
            border-left: 4px solid #d4af37;
            padding-left: 20px;
            margin-left: 0;
            color: #aaa;
            font-style: italic;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .content table th,
        .content table td {
            border: 1px solid rgba(212, 175, 55, 0.3);
            padding: 12px;
            text-align: left;
        }

        .content table th {
            background: rgba(212, 175, 55, 0.1);
            color: #d4af37;
            font-weight: 600;
        }

        .back-link {
            display: inline-block;
            margin-top: 40px;
            padding: 14px 35px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            color: #000;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
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
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="/images/logo.png" alt="MAAF Logo" class="logo-image" />
            </div>
            <h1>{$title}</h1>
        </div>

        <div class="content">
            {$content}
        </div>

        <div style="text-align: center;">
            <a href="/docs" class="back-link">← Vissza a dokumentációhoz</a>
            <a href="/" class="back-link" style="margin-left: 15px;">← Vissza a főoldalra</a>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getNotFoundHtml(string $slug): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentáció nem található - MAAF</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
        }
        h1 { color: #d4af37; margin-bottom: 20px; }
        a { color: #d4af37; text-decoration: none; }
    </style>
</head>
<body>
    <div>
        <h1>404 - Dokumentáció nem található</h1>
        <p>A(z) "{$slug}" dokumentáció nem található.</p>
        <p><a href="/docs">← Vissza a dokumentációhoz</a></p>
    </div>
</body>
</html>
HTML;
    }

    private function markdownToHtml(string $markdown): string
    {
        // Egyszerű markdown-to-HTML konverzió
        $html = $markdown;
        
        // Headers
        $html = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $html);
        
        // Bold
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        
        // Italic
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
        
        // Code blocks
        $html = preg_replace('/```(\w+)?\n(.*?)```/s', '<pre><code>$2</code></pre>', $html);
        
        // Inline code
        $html = preg_replace('/`(.*?)`/', '<code>$1</code>', $html);
        
        // Links
        $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $html);
        
        // Lists
        $html = preg_replace('/^\* (.*?)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/^(\d+)\. (.*?)$/m', '<li>$2</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ul>$0</ul>', $html);
        
        // Paragraphs
        $html = preg_replace('/\n\n/', '</p><p>', $html);
        $html = '<p>' . $html . '</p>';
        
        // Clean up empty paragraphs
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);
        $html = preg_replace('/<p>(<[h1-6])/', '$1', $html);
        $html = preg_replace('/(<\/[h1-6]>)<\/p>/', '$1', $html);
        $html = preg_replace('/<p>(<ul>)/', '$1', $html);
        $html = preg_replace('/(<\/ul>)<\/p>/', '$1', $html);
        $html = preg_replace('/<p>(<pre>)/', '$1', $html);
        $html = preg_replace('/(<\/pre>)<\/p>/', '$1', $html);
        
        return $html;
    }
}

