<?php

use Illuminate\Support\Facades\Artisan;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $exitCode = Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_04_09_210000_create_fatwa_likes_table.php',
        '--force' => true
    ]);
    echo "<h2>Migration Status</h2><pre>" . Artisan::output() . "</pre>";
    echo $exitCode === 0
        ? "<p style='color:green;font-weight:bold;'>✅ fatwa_likes table created successfully.</p>"
        : "<p style='color:red;'>❌ Migration failed.</p>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>Exception: " . $e->getMessage() . "</p>";
}
echo "<hr><p><small><b>Delete this file after use!</b></small></p>";
