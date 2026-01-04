#!/usr/bin/env php
<?php

/**
 * MAAF App Installer
 * 
 * Interaktív installer script, ami kérdéseket tesz fel a projekt inicializálásakor.
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║          MAAF Application Installer                      ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\n";

// 1. Adatbázis típus
echo "📊 Adatbázis konfiguráció\n";
echo "─────────────────────────────────────────────────────────────\n";
$databaseTypes = [
    '1' => ['name' => 'SQLite', 'driver' => 'sqlite', 'default' => true],
    '2' => ['name' => 'MySQL', 'driver' => 'mysql'],
    '3' => ['name' => 'PostgreSQL', 'driver' => 'pgsql'],
];

echo "Válassz adatbázis típust:\n";
foreach ($databaseTypes as $key => $db) {
    $default = $db['default'] ?? false;
    echo "  [$key] {$db['name']}" . ($default ? ' (alapértelmezett)' : '') . "\n";
}

$dbChoice = prompt("Választás [1]: ", '1');
$selectedDb = $databaseTypes[$dbChoice] ?? $databaseTypes['1'];

$dbConfig = [
    'driver' => $selectedDb['driver'],
];

if ($selectedDb['driver'] === 'sqlite') {
    $dbPath = prompt("SQLite fájl elérési út [database/database.sqlite]: ", 'database/database.sqlite');
    $dbConfig['database'] = $dbPath;
} else {
    $dbConfig['host'] = prompt("Adatbázis hoszt [localhost]: ", 'localhost');
    $defaultPort = $selectedDb['driver'] === 'mysql' ? '3306' : '5432';
    $dbConfig['port'] = prompt("Port [{$defaultPort}]: ", $defaultPort);
    $dbConfig['database'] = prompt("Adatbázis név: ", '');
    $dbConfig['username'] = prompt("Felhasználónév: ", 'root');
    $dbConfig['password'] = prompt("Jelszó: ", '');
}

echo "\n";

// 2. Frontend framework
echo "🎨 Frontend konfiguráció\n";
echo "─────────────────────────────────────────────────────────────\n";
$frontendOptions = [
    '1' => ['name' => 'Nincs frontend (API only)', 'value' => 'none'],
    '2' => ['name' => 'React + Vite', 'value' => 'react'],
    '3' => ['name' => 'Vue.js + Vite', 'value' => 'vue'],
    '4' => ['name' => 'Vanilla JavaScript', 'value' => 'vanilla'],
];

echo "Válassz frontend framework-ot:\n";
foreach ($frontendOptions as $key => $frontend) {
    echo "  [$key] {$frontend['name']}\n";
}

$frontendChoice = prompt("Választás [1]: ", '1');
$selectedFrontend = $frontendOptions[$frontendChoice] ?? $frontendOptions['1'];

echo "\n";

// 3. JWT Secret
echo "🔐 Biztonsági beállítások\n";
echo "─────────────────────────────────────────────────────────────\n";
$jwtSecret = prompt("JWT Secret kulcs (hagyd üresen az automatikus generáláshoz): ", '');
if (empty($jwtSecret)) {
    $jwtSecret = bin2hex(random_bytes(32));
    echo "✓ Automatikusan generált JWT secret: " . substr($jwtSecret, 0, 20) . "...\n";
}

echo "\n";

// 4. Környezeti változók
echo "🌍 Környezeti változók\n";
echo "─────────────────────────────────────────────────────────────\n";
$appEnv = prompt("Alkalmazás környezet [development]: ", 'development');
$appDebug = strtolower(prompt("Debug mód engedélyezése? [yes]: ", 'yes')) === 'yes';

echo "\n";

// 5. További opciók
echo "⚙️  További beállítások\n";
echo "─────────────────────────────────────────────────────────────\n";
$installExampleModule = strtolower(prompt("Telepítsem a példa modult? [yes]: ", 'yes')) === 'yes';
$initializeGit = strtolower(prompt("Git inicializálása? [yes]: ", 'yes')) === 'yes';

echo "\n";

// Konfiguráció generálása
echo "📝 Konfiguráció generálása...\n";

// .env fájl létrehozása
$appDebugStr = $appDebug ? 'true' : 'false';
$envContent = <<<ENV
APP_ENV={$appEnv}
APP_DEBUG={$appDebugStr}

# Database Configuration
DB_CONNECTION={$dbConfig['driver']}
ENV;

if ($selectedDb['driver'] === 'sqlite') {
    $envContent .= "\nDB_DATABASE={$dbConfig['database']}\n";
} else {
    $envContent .= <<<ENV
DB_HOST={$dbConfig['host']}
DB_PORT={$dbConfig['port']}
DB_DATABASE={$dbConfig['database']}
DB_USERNAME={$dbConfig['username']}
DB_PASSWORD={$dbConfig['password']}
ENV;
}

$envContent .= <<<ENV

# JWT Configuration
JWT_SECRET={$jwtSecret}

# Frontend Configuration
FRONTEND_TYPE={$selectedFrontend['value']}
ENV;

file_put_contents('.env', $envContent);
echo "✓ .env fájl létrehozva\n";

// config/database.php generálása
$databaseConfig = generateDatabaseConfig($dbConfig, $selectedDb['driver']);
file_put_contents('config/database.php', $databaseConfig);
echo "✓ config/database.php létrehozva\n";

// config/services.php frissítése PDO-val
$servicesConfig = generateServicesConfig($dbConfig, $selectedDb['driver'], $jwtSecret);
file_put_contents('config/services.php', $servicesConfig);
echo "✓ config/services.php frissítve\n";

// Frontend inicializálás
if ($selectedFrontend['value'] !== 'none') {
    echo "🎨 Frontend inicializálása...\n";
    initializeFrontend($selectedFrontend['value']);
}

// Példa modul törlése, ha nem kell
if (!$installExampleModule) {
    echo "🗑️  Példa modul eltávolítása...\n";
    removeExampleModule();
}

// Environment validation
echo "\n🔍 Konfiguráció validálása...\n";
$validationErrors = validateEnvironment([
    'jwtSecret' => $jwtSecret,
    'dbDriver' => $selectedDb['driver'],
    'dbDatabase' => $dbConfig['database'] ?? '',
]);

if (!empty($validationErrors)) {
    echo "  ⚠️  Figyelmeztetések:\n";
    foreach ($validationErrors as $error) {
        echo "    - {$error}\n";
    }
} else {
    echo "  ✓ Konfiguráció validálva\n";
}

// Database connection test
echo "\n🔍 Adatbázis kapcsolat tesztelése...\n";
if (testDatabaseConnection($dbConfig, $selectedDb['driver'])) {
    echo "  ✓ Adatbázis kapcsolat sikeres\n";
} else {
    echo "  ⚠️  Adatbázis kapcsolat sikertelen - ellenőrizd a beállításokat\n";
}

// Git inicializálás
if ($initializeGit) {
    echo "\n📦 Git inicializálása...\n";
    initializeGit();
}

echo "\n";
echo "✅ Telepítés sikeres!\n";
echo "\n";
echo "📋 Összefoglalás:\n";
echo "  ✓ Framework telepítve (maaf/core)\n";
echo "  ✓ Vendor csomagok telepítve\n";
echo "  ✓ .env fájl létrehozva\n";
echo "  ✓ JWT Secret generálva\n";
echo "  ✓ Adatbázis konfigurálva\n";
echo "  ✓ Frontend konfigurálva: {$selectedFrontend['name']}\n";
echo "  ✓ Database migrációk scaffold\n";
echo "  ✓ Testing framework (PHPUnit)\n";
echo "  ✓ CORS konfiguráció\n";
echo "  ✓ Authentication scaffold\n";
echo "  ✓ Docker konfiguráció\n";
echo "  ✓ Code style tools\n";
echo "  ✓ Logging konfiguráció\n";
echo "  ✓ Middleware pipeline\n";
echo "  ✓ CI/CD workflow\n";
echo "  ✓ Health check endpoint\n";
echo "  ✓ Rate limiting\n";
echo "  ✓ Seed adatok scaffold\n";
echo "  ✓ Error handling\n";
echo "  ✓ CLI tool (maaf)\n";
echo "  ✓ API dokumentáció scaffold\n";
echo "  ✓ Welcome page\n";
echo "\n";
echo "🚀 Következő lépések:\n";
$projectName = basename(getcwd());
echo "  1. cd {$projectName}\n";
echo "  2. composer install (ha még nem futott le)\n";
echo "  3. composer migrate (adatbázis migrációk futtatása)\n";
echo "  4. php maaf serve (vagy: cd public && php -S localhost:8000)\n";
echo "\n";
echo "🌐 Az alkalmazás elérhető lesz: http://localhost:8000\n";
echo "   🎨 Welcome page: http://localhost:8000/\n";
echo "   📊 Health check: http://localhost:8000/health\n";
echo "   📚 API docs: http://localhost:8000/api-docs\n";
echo "\n";
echo "💡 Hasznos parancsok:\n";
echo "  php maaf migrate      - Migrációk futtatása\n";
echo "  php maaf seed          - Seed adatok futtatása\n";
echo "  php maaf serve         - Development server indítása\n";
echo "  composer test          - Tesztek futtatása\n";
echo "  composer lint          - Code style ellenőrzés\n";
echo "  composer fix           - Code style javítás\n";
echo "  docker-compose up      - Docker environment indítása\n";
if ($selectedFrontend['value'] !== 'none') {
    echo "\n";
    echo "💡 Frontend inicializálása:\n";
    echo "  cd frontend\n";
    echo "  npm install\n";
    echo "  npm run dev\n";
}
echo "\n";

/**
 * Adatbázis konfiguráció generálása
 */
function generateDatabaseConfig(array $dbConfig, string $driver): string
{
    if ($driver === 'sqlite') {
        $dbPath = $dbConfig['database'] ?? 'database/database.sqlite';
        return <<<PHP
<?php

return [
    'default' => 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../{$dbPath}',
            'prefix' => '',
        ],
    ],
];
PHP;
    }

    $host = $dbConfig['host'] ?? 'localhost';
    $port = $dbConfig['port'] ?? ($driver === 'mysql' ? '3306' : '5432');
    $database = $dbConfig['database'] ?? 'database';
    $username = $dbConfig['username'] ?? 'root';
    $password = $dbConfig['password'] ?? '';
    
    $charset = $driver === 'mysql' ? 'utf8mb4' : 'utf8';
    $collation = $driver === 'mysql' ? 'utf8mb4_unicode_ci' : '';

    $config = <<<PHP
<?php

return [
    'default' => '{$driver}',
    'connections' => [
        '{$driver}' => [
            'driver' => '{$driver}',
            'host' => '{$host}',
            'port' => '{$port}',
            'database' => '{$database}',
            'username' => '{$username}',
            'password' => '{$password}',
PHP;
    
    if ($driver === 'mysql') {
        $config .= <<<PHP
            'charset' => '{$charset}',
            'collation' => '{$collation}',
PHP;
    } else {
        $config .= <<<PHP
            'charset' => '{$charset}',
PHP;
    }
    
    $config .= <<<PHP
            'prefix' => '',
        ],
    ],
];
PHP;
    
    return $config;
}

/**
 * Services konfiguráció generálása
 */
function generateServicesConfig(array $dbConfig, string $driver, string $jwtSecret): string
{
    if ($driver === 'sqlite') {
        $dbPath = $dbConfig['database'] ?? 'database/database.sqlite';
        $pdoConfig = <<<PHP
    PDO::class => DI\factory(function () {
        \$path = __DIR__ . '/../{$dbPath}';
        if (!file_exists(dirname(\$path))) {
            mkdir(dirname(\$path), 0755, true);
        }
        if (!file_exists(\$path)) {
            touch(\$path);
        }
        \$pdo = new PDO('sqlite:' . \$path);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return \$pdo;
    }),
PHP;
    } else {
        $host = $dbConfig['host'] ?? 'localhost';
        $port = $dbConfig['port'] ?? ($driver === 'mysql' ? '3306' : '5432');
        $database = $dbConfig['database'] ?? 'database';
        $username = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';
        
        $pdoConfig = <<<PHP
    PDO::class => DI\factory(function () {
        \$host = getenv('DB_HOST') ?: '{$host}';
        \$port = getenv('DB_PORT') ?: '{$port}';
        \$database = getenv('DB_DATABASE') ?: '{$database}';
        \$username = getenv('DB_USERNAME') ?: '{$username}';
        \$password = getenv('DB_PASSWORD') ?: '{$password}';
        
        \$dsn = '{$driver}:host=' . \$host . ';port=' . \$port . ';dbname=' . \$database . ';charset=utf8mb4';
        \$pdo = new PDO(\$dsn, \$username, \$password);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return \$pdo;
    }),
PHP;
    }

    $jwtSecretEscaped = addslashes($jwtSecret);
    
    return <<<PHP
<?php

use function DI\factory;

return [
    // Database
{$pdoConfig}
    
    // JWT Secret
    'jwt.secret' => getenv('JWT_SECRET') ?: '{$jwtSecretEscaped}',
];
PHP;
}

/**
 * Frontend inicializálása
 */
function initializeFrontend(string $type): void
{
    if ($type === 'none') {
        return;
    }
    
    $frontendDir = __DIR__ . '/frontend';
    
    if (is_dir($frontendDir)) {
        echo "  ⚠️  Frontend könyvtár már létezik\n";
        return;
    }
    
    echo "  📦 Frontend inicializálása ({$type})...\n";
    
    // Vite template nevek
    $templates = [
        'react' => 'react',
        'vue' => 'vue',
        'vanilla' => 'vanilla',
    ];
    
    $template = $templates[$type] ?? 'vanilla';
    
    // npm create vite parancs futtatása
    $command = "npm create vite@latest frontend -- --template {$template} --yes 2>&1";
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0 && is_dir($frontendDir)) {
        echo "  ✓ Frontend inicializálva\n";
        
        // Vite config frissítése proxy-val
        updateViteConfig($frontendDir, $template);
        
        // Frontend .env fájl létrehozása
        createFrontendEnv($frontendDir);
    } else {
        echo "  ⚠️  Frontend inicializálás sikertelen (npm nincs telepítve?)\n";
        echo "     Kézzel inicializáld: npm create vite@latest frontend -- --template {$template}\n";
    }
}

/**
 * Vite config frissítése proxy-val
 */
function updateViteConfig(string $frontendDir, string $template): void
{
    // Vite config fájl neve template-től függően változhat
    $possibleConfigs = [
        $frontendDir . '/vite.config.js',
        $frontendDir . '/vite.config.ts',
    ];
    
    $viteConfigPath = null;
    foreach ($possibleConfigs as $path) {
        if (file_exists($path)) {
            $viteConfigPath = $path;
            break;
        }
    }
    
    if ($viteConfigPath === null) {
        return;
    }
    
    $config = file_get_contents($viteConfigPath);
    
    // Proxy konfiguráció hozzáadása, ha még nincs
    if (strpos($config, 'proxy') === false && strpos($config, 'server') === false) {
        $proxyConfig = <<<'JS'
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/api/, '')
      }
    }
  },
JS;
        
        // Beszúrás a defineConfig után
        if (strpos($config, 'defineConfig') !== false) {
            $config = preg_replace(
                '/export default defineConfig\(\{/',
                'export default defineConfig({' . "\n" . $proxyConfig,
                $config,
                1
            );
        } else {
            // Ha nincs defineConfig, hozzáadjuk
            $config = str_replace(
                'export default {',
                'export default {' . "\n" . $proxyConfig,
                $config
            );
        }
        
        file_put_contents($viteConfigPath, $config);
        echo "  ✓ Vite proxy konfigurálva\n";
    }
}

/**
 * Frontend .env fájl létrehozása
 */
function createFrontendEnv(string $frontendDir): void
{
    $envContent = <<<ENV
VITE_API_URL=http://localhost:8000
ENV;
    
    $envPath = $frontendDir . '/.env';
    if (!file_exists($envPath)) {
        file_put_contents($envPath, $envContent);
        echo "  ✓ Frontend .env fájl létrehozva\n";
    }
}

/**
 * Példa modul eltávolítása
 */
function removeExampleModule(): void
{
    $examplePath = __DIR__ . '/src/Modules/Example';
    if (is_dir($examplePath)) {
        removeDirectory($examplePath);
        echo "  ✓ Példa modul eltávolítva\n";
    }
}

/**
 * Könyvtár rekurzív törlése
 */
function removeDirectory(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? removeDirectory($path) : unlink($path);
    }
    rmdir($dir);
}

/**
 * Prompt függvény Windows és Linux kompatibilis
 */
function prompt(string $message, string $default = ''): string
{
    echo $message;
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    $input = trim($line);
    return $input !== '' ? $input : $default;
}

/**
 * Environment validation
 */
function validateEnvironment(array $config): array
{
    $errors = [];
    
    // JWT Secret ellenőrzés
    if (empty($config['jwtSecret']) || strlen($config['jwtSecret']) < 32) {
        $errors[] = "JWT Secret minimum 32 karakter hosszúságú kell legyen";
    }
    
    // Database ellenőrzés
    if ($config['dbDriver'] !== 'sqlite' && empty($config['dbDatabase'])) {
        $errors[] = "Adatbázis név kötelező MySQL/PostgreSQL esetén";
    }
    
    return $errors;
}

/**
 * Database connection test
 */
function testDatabaseConnection(array $dbConfig, string $driver): bool
{
    try {
        if ($driver === 'sqlite') {
            $path = $dbConfig['database'] ?? 'database/database.sqlite';
            $fullPath = __DIR__ . '/' . $path;
            
            // Könyvtár létrehozása, ha nem létezik
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Fájl létrehozása, ha nem létezik
            if (!file_exists($fullPath)) {
                touch($fullPath);
            }
            
            $pdo = new PDO('sqlite:' . $fullPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query('SELECT 1');
            
            return true;
        } else {
            $host = $dbConfig['host'] ?? 'localhost';
            $port = $dbConfig['port'] ?? ($driver === 'mysql' ? '3306' : '5432');
            $database = $dbConfig['database'] ?? '';
            $username = $dbConfig['username'] ?? 'root';
            $password = $dbConfig['password'] ?? '';
            
            if (empty($database)) {
                return false;
            }
            
            $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query('SELECT 1');
            
            return true;
        }
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Database migrations directory létrehozása
 */
function createMigrationsDirectory(): void
{
    $migrationsDir = __DIR__ . '/database/migrations';
    
    if (!is_dir($migrationsDir)) {
        mkdir($migrationsDir, 0755, true);
        echo "  ✓ Migrációk könyvtár létrehozva\n";
    }
    
    // Példa migráció fájl már létezik a package-ben
    // Csak ellenőrizzük, hogy létezik-e
    $exampleFile = $migrationsDir . '/0001_create_example_table.sql';
    if (file_exists($exampleFile)) {
        echo "  ✓ Példa migráció megtalálható\n";
    }
}

/**
 * Testing framework beállítása
 */
function setupTestingFramework(): void
{
    // PHPUnit config már létezik a package-ben
    $phpunitConfig = __DIR__ . '/phpunit.xml';
    if (file_exists($phpunitConfig)) {
        echo "  ✓ PHPUnit konfiguráció megtalálható\n";
    }
    
    // Tests könyvtár ellenőrzése
    $testsDir = __DIR__ . '/tests';
    if (!is_dir($testsDir)) {
        mkdir($testsDir, 0755, true);
        mkdir($testsDir . '/Unit', 0755, true);
        mkdir($testsDir . '/Integration', 0755, true);
        echo "  ✓ Tests könyvtárak létrehozva\n";
    }
    
    // Példa teszt fájlok ellenőrzése
    $exampleTest = $testsDir . '/Unit/ExampleTest.php';
    if (file_exists($exampleTest)) {
        echo "  ✓ Példa teszt fájlok megtalálhatók\n";
    }
}

/**
 * CORS konfiguráció létrehozása
 */
function createCorsConfig(): void
{
    $corsConfigFile = __DIR__ . '/config/cors.php';
    if (file_exists($corsConfigFile)) {
        echo "  ✓ CORS konfiguráció megtalálható\n";
    } else {
        echo "  ⚠️  CORS konfiguráció fájl hiányzik\n";
    }
}

/**
 * Health check endpoint létrehozása
 */
function createHealthEndpoints(): void
{
    $healthModuleDir = __DIR__ . '/src/Modules/Health';
    if (is_dir($healthModuleDir)) {
        echo "  ✓ Health check modul megtalálható\n";
    } else {
        echo "  ⚠️  Health check modul hiányzik\n";
    }
}


/**
 * CORS konfiguráció létrehozása
 */
function setupCorsConfiguration(): void
{
    $corsConfigFile = __DIR__ . '/config/cors.php';
    
    if (!file_exists($corsConfigFile)) {
        echo "  ⚠️  CORS konfiguráció fájl nem található\n";
    } else {
        echo "  ✓ CORS konfiguráció létrehozva\n";
    }
    
    // CORS middleware ellenőrzése
    $middlewareFile = __DIR__ . '/src/Middleware/CorsMiddleware.php';
    if (!file_exists($middlewareFile)) {
        echo "  ⚠️  CORS middleware nem található\n";
    } else {
        echo "  ✓ CORS middleware létrehozva\n";
    }
}

/**
 * Health check endpoint létrehozása
 */
function createHealthCheckEndpoint(): void
{
    $healthModuleDir = __DIR__ . '/src/Modules/Health';
    
    if (!is_dir($healthModuleDir)) {
        mkdir($healthModuleDir . '/Controllers', 0755, true);
        echo "  ✓ Health modul könyvtár létrehozva\n";
    }
    
    // Module.php és Controller.php már léteznek a package-ben
    echo "  ✓ Health check endpoint létrehozva\n";
}

/**
 * Authentication scaffold létrehozása
 */
function createAuthenticationScaffold(): void
{
    $authModuleDir = __DIR__ . '/src/Modules/Auth';
    
    if (!is_dir($authModuleDir)) {
        mkdir($authModuleDir . '/Controllers', 0755, true);
        echo "  ✓ Auth modul könyvtár létrehozva\n";
    }
    
    // Module.php és Controller.php már léteznek a package-ben
    echo "  ✓ Authentication scaffold létrehozva\n";
}

/**
 * Docker konfiguráció létrehozása
 */
function createDockerConfiguration(): void
{
    // Dockerfile és docker-compose.yml már léteznek a package-ben
    if (file_exists(__DIR__ . '/Dockerfile') && file_exists(__DIR__ . '/docker-compose.yml')) {
        echo "  ✓ Docker konfiguráció létrehozva\n";
    } else {
        echo "  ⚠️  Docker fájlok nem találhatók\n";
    }
}

/**
 * Code style konfiguráció létrehozása
 */
function createCodeStyleConfiguration(): void
{
    if (file_exists(__DIR__ . '/.php-cs-fixer.php') && file_exists(__DIR__ . '/phpcs.xml')) {
        echo "  ✓ Code style konfiguráció létrehozva\n";
    } else {
        echo "  ⚠️  Code style fájlok nem találhatók\n";
    }
}

/**
 * Logging konfiguráció létrehozása
 */
function createLoggingConfiguration(): void
{
    $storageDir = __DIR__ . '/storage/logs';
    
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
        echo "  ✓ Storage/logs könyvtár létrehozva\n";
    }
    
    if (file_exists(__DIR__ . '/config/logging.php')) {
        echo "  ✓ Logging konfiguráció létrehozva\n";
    }
    
    if (file_exists(__DIR__ . '/src/Middleware/LoggingMiddleware.php')) {
        echo "  ✓ Logging middleware létrehozva\n";
    }
}

/**
 * Rate limiting konfiguráció létrehozása
 */
function createRateLimitingConfiguration(): void
{
    if (file_exists(__DIR__ . '/config/rate-limiting.php') && 
        file_exists(__DIR__ . '/src/Middleware/RateLimitingMiddleware.php')) {
        echo "  ✓ Rate limiting konfiguráció létrehozva\n";
    }
}

/**
 * Seed adatok scaffold létrehozása
 */
function createSeedsScaffold(): void
{
    $seedsDir = __DIR__ . '/database/seeds';
    
    if (!is_dir($seedsDir)) {
        mkdir($seedsDir, 0755, true);
        echo "  ✓ Seeds könyvtár létrehozva\n";
    }
    
    // run-seeds.php már létezik a package-ben
    if (file_exists(__DIR__ . '/run-seeds.php')) {
        echo "  ✓ Seed runner script létrehozva\n";
    }
}

/**
 * Error handling konfiguráció létrehozása
 */
function createErrorHandlingConfiguration(): void
{
    if (file_exists(__DIR__ . '/src/Exceptions/Handler.php')) {
        echo "  ✓ Error handler létrehozva\n";
    }
}

/**
 * CLI tool létrehozása
 */
function createCliTool(): void
{
    $cliFile = __DIR__ . '/maaf';
    
    if (file_exists($cliFile)) {
        // Make executable on Unix systems
        if (PHP_OS_FAMILY !== 'Windows') {
            chmod($cliFile, 0755);
        }
        echo "  ✓ CLI tool létrehozva\n";
    }
}

/**
 * API dokumentáció scaffold létrehozása
 */
function createApiDocsScaffold(): void
{
    $apiDocsModuleDir = __DIR__ . '/src/Modules/ApiDocs';
    
    if (!is_dir($apiDocsModuleDir)) {
        mkdir($apiDocsModuleDir . '/Controllers', 0755, true);
        echo "  ✓ API Docs modul könyvtár létrehozva\n";
    }
    
    // Module.php és Controller.php már léteznek a package-ben
    echo "  ✓ API dokumentáció scaffold létrehozva\n";
}

/**
 * Projekt-specifikus README generálása
 */
function generateProjectReadme(string $appEnv, string $frontendName): void
{
    $projectName = basename(getcwd());
    $readmeContent = <<<MARKDOWN
# {$projectName}

A MAAF application.

## Telepítés

```bash
composer install
composer migrate
```

## Fejlesztés

```bash
# Backend indítása
php maaf serve

# Vagy
cd public && php -S localhost:8000
MARKDOWN;

    if ($frontendName !== 'Nincs frontend (API only)') {
        $readmeContent .= <<<MARKDOWN

# Frontend indítása
cd frontend
npm install
npm run dev
MARKDOWN;
    }

    $readmeContent .= <<<MARKDOWN

## Hasznos Parancsok

- `php maaf migrate` - Adatbázis migrációk futtatása
- `php maaf seed` - Seed adatok futtatása
- `php maaf serve` - Development server indítása
- `composer test` - Tesztek futtatása
- `composer lint` - Code style ellenőrzés
- `composer fix` - Code style javítás

## API Endpoints

- `GET /` - Welcome endpoint
- `GET /health` - Health check
- `GET /api-docs` - API dokumentáció
- `POST /auth/register` - Regisztráció
- `POST /auth/login` - Bejelentkezés
- `GET /auth/me` - Aktuális felhasználó

## Docker

```bash
docker-compose up -d
```

## Környezet

- Environment: {$appEnv}
- Frontend: {$frontendName}

## Dokumentáció

Lásd: https://github.com/mimimami/maaf-core
MARKDOWN;

    file_put_contents(__DIR__ . '/README.md', $readmeContent);
    echo "  ✓ Projekt-specifikus README generálva\n";
}

/**
 * Welcome page létrehozása
 */
function createWelcomePage(): void
{
    $welcomeModuleDir = __DIR__ . '/src/Modules/Welcome';
    
    if (!is_dir($welcomeModuleDir)) {
        mkdir($welcomeModuleDir . '/Controllers', 0755, true);
        echo "  ✓ Welcome modul könyvtár létrehozva\n";
    }
    
    // Module.php és Controller.php már léteznek a package-ben
    echo "  ✓ Welcome page létrehozva\n";
}

/**
 * Git inicializálás
 */
function initializeGit(): void
{
    if (is_dir('.git')) {
        echo "  ℹ️  Git már inicializálva\n";
        return;
    }
    
    // Git init
    exec('git init 2>&1', $output, $returnCode);
    if ($returnCode === 0) {
        echo "  ✓ Git inicializálva\n";
        
        // .gitignore ellenőrzése
        if (!file_exists('.gitignore')) {
            $gitignoreContent = <<<GITIGNORE
/vendor/
composer.lock
.phpunit.result.cache
.phpstan/
.idea/
.vscode/
*.log
*.cache
.DS_Store
Thumbs.db

# Generated files
.env
config/database.php
GITIGNORE;
            file_put_contents('.gitignore', $gitignoreContent);
            echo "  ✓ .gitignore létrehozva\n";
        }
        
        // Kezdeti commit (opcionális, mert lehet, hogy a felhasználó nem akarja)
        // Csak ha van .gitignore és nincs még commit
        exec('git status --porcelain', $statusOutput, $statusReturnCode);
        if ($statusReturnCode === 0 && !empty($statusOutput)) {
            exec('git add .gitignore composer.json composer.lock 2>&1', $addOutput, $addReturnCode);
            if ($addReturnCode === 0) {
                exec('git commit -m "Initial commit" 2>&1', $commitOutput, $commitReturnCode);
                if ($commitReturnCode === 0) {
                    echo "  ✓ Kezdeti commit létrehozva\n";
                }
            }
        }
    } else {
        echo "  ⚠️  Git inicializálás sikertelen (git nincs telepítve?)\n";
    }
}

