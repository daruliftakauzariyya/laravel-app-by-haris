<?php

/**
 * Category Seeder Runner for Live Server
 * 
 * Usage: domain.com/public/database/seeders/run.php?token=fatwa_seed_2026
 * 
 * Instructions:
 * 1. Upload this file to your public/database/seeders/ directory.
 * 2. Visit the URL in your browser to run the category seeder.
 */

$token = 'fatwa_seed_2026'; // Secret token for security

if (!isset($_GET['token']) || $_GET['token'] !== $token) {
    die('Unauthorized access. Please provide the correct token.');
}

// Bootstrap Laravel
require __DIR__.'/../../../vendor/autoload.php';
$app = require_once __DIR__.'/../../../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

try {
    echo "<h1>Fatwa Portal: Category Seeding</h1>";
    echo "<p>Bootstrapping Laravel... Done.</p>";
    
    echo "<h3>Running CategorySeeder...</h3>";
    
    // We use --force because it's a production environment
    Artisan::call('db:seed', [
        '--class' => 'CategorySeeder',
        '--force' => true
    ]);
    
    echo "<pre style='background:#f4f4f4; padding:15px; border:1px solid #ddd;'>" . Artisan::output() . "</pre>";
    
    echo "<br><b style='color:green;'>Categories Seeded Successfully!</b>";
    echo "<p>You can now check the <a href='/admin/categories'>Category Management</a> page in your admin panel.</p>";
    
} catch (Exception $e) {
    echo "<b style='color:red;'>Error during seeding:</b><br>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    Log::error("Seeding Error: " . $e->getMessage());
}
