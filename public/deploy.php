<?php

/**
 * Laravel Web Deployer & Migration Runner
 * 
 * Usage: domain.com/deploy.php?token=YOUR_SECRET_TOKEN
 * 
 * Instructions:
 * 1. Upload this file to your public/ directory on the live server.
 * 2. Set a secret token below.
 * 3. Visit the URL in your browser to run migrations.
 */

$token = 'fatwa_deploy_2026'; // Change this to a secure token before uploading!

if ($_GET['token'] !== $token) {
    die('Unauthorized access.');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

try {
    echo "<h1>Laravel Deployment Status</h1>";
    
    echo "<h3>1. Running Migrations...</h3>";
    Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Artisan::output() . "</pre>";
    
    echo "<h3>2. Clearing Cache...</h3>";
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    echo "Done.<br>";

    echo "<h3>3. Seeding Database (Optional)...</h3>";
    // Uncomment the next line if you need to seed initial data
    // Artisan::call('db:seed', ['--force' => true]);
    
    echo "<br><b style='color:green;'>Deployment Step Completed Successfully!</b>";
    
} catch (Exception $e) {
    echo "<b style='color:red;'>Error during deployment:</b><br>";
    echo $e->getMessage();
}
