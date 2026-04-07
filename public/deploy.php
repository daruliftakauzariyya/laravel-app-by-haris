<?php

/**
 * Laravel Web Deployer & Cache Cleanser (Laravel 11 Version)
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

try {
    header('Content-Type: text/html; charset=utf-8');
    echo "<body style='font-family: sans-serif; padding: 40px; background: #f4f7f9; color: #333;'>";
    echo "<div style='max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>";
    echo "<h1 style='color: #4A6372; margin-top: 0;'>Server Cache Cleansing</h1>";
    echo "<hr style='opacity:0.1; margin-bottom: 25px;'>";
    
    $commands = [
        'config:clear' => 'Configuration Cache',
        'route:clear'  => 'Routes Cache',
        'view:clear'   => 'Views & Templates',
        'cache:clear'  => 'General App Memory',
        'optimize:clear' => 'Optimization Files'
    ];

    foreach ($commands as $command => $label) {
        echo "<div style='margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;'>";
        echo "<span>Clearing <b>$label</b>...</span>";
        Artisan::call($command);
        echo "<span style='color: #1a7a4a; font-weight: bold;'>[ DONE ]</span>";
        echo "</div>";
    }

    echo "<hr style='opacity:0.1; margin: 25px 0;'>";
    echo "<div style='text-align: center;'>";
    echo "<b style='color: #1a7a4a; font-size: 1.25rem;'>✓ Success: All Caches Cleared!</b>";
    echo "<p style='color: #666;'>Your latest UI edits and design changes are now live.</p>";
    echo "<a href='/admin/dashboard' style='display: inline-block; margin-top: 15px; padding: 10px 25px; background: #4A6372; color: #fff; text-decoration: none; border-radius: 6px;'>Back to Dashboard</a>";
    echo "</div>";
    echo "</div></body>";
    
} catch (Exception $e) {
    echo "<div style='max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; border-left: 5px solid red;'>";
    echo "<b style='color:red;'>System Error:</b><br>";
    echo "<pre style='background: #f8f9fa; padding: 10px; margin-top:10px;'>" . $e->getMessage() . "</pre>";
    echo "</div>";
}
