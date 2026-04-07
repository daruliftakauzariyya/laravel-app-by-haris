<?php

/**
 * Laravel Web Deployer & Migration Runner (Laravel 11 Version)
 * 
 * Usage: domain.com/deploy.php?token=fatwa_deploy_2026
 */

$token = 'fatwa_deploy_2026'; // Token for security

if (!isset($_GET['token']) || $_GET['token'] !== $token) {
    die('Unauthorized access. Please provide the correct token.');
}

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// IMPORTANT for Laravel 11+: Bootstrap the Console Kernel to enable Artisan Facade in web context
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

try {
    echo "<h1>Laravel 11 Deployment Status</h1>";
    echo "<p>Bootstrapping Laravel... Done.</p>";
    
    echo "<h3>1. Running Migrations...</h3>";
    Artisan::call('migrate', ['--force' => true]);
    echo "<pre style='background:#f8f9fa; border:1px solid #ddd; padding:10px;'>" . Artisan::output() . "</pre>";
    
    echo "<h3>2. Clearing All Caches...</h3>";
    
    echo "Cleaning Config cache... ";
    Artisan::call('config:clear');
    echo "Done.<br>";
    
    echo "Cleaning Route cache... ";
    Artisan::call('route:clear');
    echo "Done.<br>";
    
    echo "Cleaning View cache... ";
    Artisan::call('view:clear');
    echo "Done.<br>";
    
    echo "Cleaning General cache... ";
    Artisan::call('cache:clear');
    echo "Done.<br>";

    echo "<br><b style='color:green; font-size:1.2rem;'>✓ Deployment & Cache Clearing Completed Successfully!</b>";
    echo "<p>Navigate back to <a href='/admin/dashboard'>Admin Dashboard</a>.</p>";
    
} catch (Exception $e) {
    echo "<b style='color:red;'>Error during deployment:</b><br>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    Log::error("Deployment Error: " . $e->getMessage());
}
