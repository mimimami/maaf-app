#!/usr/bin/env php
<?php

/**
 * Migration Runner
 * 
 * Runs all pending migrations from the database/migrations/ directory.
 */

require_once __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use PDO;

// Build DI container
$containerBuilder = new ContainerBuilder();
if (file_exists(__DIR__ . '/config/services.php')) {
    $containerBuilder->addDefinitions(__DIR__ . '/config/services.php');
}
$container = $containerBuilder->build();

// Get PDO instance
$pdo = $container->get(PDO::class);

// Create migrations table if it doesn't exist
$pdo->exec('
    CREATE TABLE IF NOT EXISTS migrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        filename TEXT NOT NULL UNIQUE,
        executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
');

// Get all migration files
$migrationsDir = __DIR__ . '/database/migrations';
if (!is_dir($migrationsDir)) {
    echo "❌ Migrations directory not found: {$migrationsDir}\n";
    exit(1);
}

$files = glob($migrationsDir . '/*.sql');
if (empty($files)) {
    echo "ℹ️  No migration files found.\n";
    exit(0);
}

// Sort files by name (they should have timestamp prefix)
sort($files);

// Get already executed migrations
$executedMigrations = $pdo->query('SELECT filename FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
$executedMigrations = array_flip($executedMigrations);

$executed = 0;
$skipped = 0;

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip example files
    if (str_ends_with($filename, '.example')) {
        continue;
    }
    
    // Skip if already executed
    if (isset($executedMigrations[$filename])) {
        echo "⏭️  Skipping {$filename} (already executed)\n";
        $skipped++;
        continue;
    }
    
    echo "🔄 Running migration: {$filename}\n";
    
    try {
        $pdo->beginTransaction();
        
        // Read and execute migration
        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new RuntimeException("Could not read migration file: {$file}");
        }
        
        $pdo->exec($sql);
        
        // Record migration
        $stmt = $pdo->prepare('INSERT INTO migrations (filename) VALUES (?)');
        $stmt->execute([$filename]);
        
        $pdo->commit();
        
        echo "  ✓ Migration executed successfully\n";
        $executed++;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "  ❌ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n";
echo "✅ Migrations completed!\n";
echo "   Executed: {$executed}\n";
echo "   Skipped: {$skipped}\n";
