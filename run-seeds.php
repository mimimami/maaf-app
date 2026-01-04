#!/usr/bin/env php
<?php

/**
 * Seed Runner
 * 
 * Runs all seeders from the database/seeds/ directory.
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

// Get all seeder files
$seedsDir = __DIR__ . '/database/seeds';
if (!is_dir($seedsDir)) {
    echo "❌ Seeds directory not found: {$seedsDir}\n";
    exit(1);
}

$files = glob($seedsDir . '/*.php');
if (empty($files)) {
    echo "ℹ️  No seeder files found.\n";
    exit(0);
}

// Sort files by name
sort($files);

$executed = 0;
$skipped = 0;

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip example files
    if (str_ends_with($filename, '.example')) {
        continue;
    }
    
    echo "🔄 Running seeder: {$filename}\n";
    
    try {
        // Include and execute seeder
        require $file;
        $executed++;
        echo "  ✓ Seeder executed successfully\n";
    } catch (Exception $e) {
        echo "  ❌ Seeder failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n";
echo "✅ Seeders completed!\n";
echo "   Executed: {$executed}\n";

