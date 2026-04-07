<?php

/**
 * Laravel Web Deployer — Cache Clear + Migrate
 * 
 * Clear caches : domain.com/deploy.php?token=fatwa_deploy_2026
 * Run migrations: domain.com/deploy.php?token=fatwa_deploy_2026&action=migrate
 */

$token = 'fatwa_deploy_2026';

if (!isset($_GET['token']) || $_GET['token'] !== $token) {
    die('Unauthorized access. Please provide the correct token.');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;

$action = $_GET['action'] ?? 'cache';

header('Content-Type: text/html; charset=utf-8');
echo "<body style='font-family: sans-serif; padding: 40px; background: #f4f7f9; color: #333;'>";
echo "<div style='max-width: 660px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.07);'>";

try {

    // ─── MIGRATION MODE ────────────────────────────────────────────
    if ($action === 'migrate') {
        echo "<h1 style='color:#1a7a4a; margin-top:0;'>🗄️ Running New Migrations</h1>";
        echo "<hr style='opacity:0.1; margin-bottom:20px;'>";

        // Only run these specific new migration files — skip all old ones
        $newMigrations = [
            '2026_04_07_000001_add_featured_image_to_fatwas',
            '2026_04_07_200000_create_fatwa_user_views_table',
            '2026_04_07_210000_create_fatwa_activity_logs_table',
            '2026_04_07_220000_create_portal_visitor_logs_table',
        ];

        foreach ($newMigrations as $migration) {
            echo "<div style='margin-bottom:14px; padding:10px 14px; border-radius:6px; background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #1a7a4a;'>";
            echo "<b style='color:#2d3748;'>Running:</b> <code style='font-size:0.85rem; color:#1a7a4a;'>{$migration}</code><br>";
            try {
                Artisan::call('migrate', [
                    '--force' => true,
                    '--path'  => "database/migrations/{$migration}.php",
                ]);
                $out = trim(Artisan::output());
                echo "<span style='color:#1a7a4a; font-weight:bold;'>✓ Done</span>";
                if ($out) {
                    echo "<pre style='margin:6px 0 0; font-size:0.78rem; color:#555; white-space:pre-wrap;'>" . htmlspecialchars($out) . "</pre>";
                }
            } catch (\Exception $me) {
                $msg = $me->getMessage();
                if (str_contains($msg, 'already exists')) {
                    echo "<span style='color:#c9a84c; font-weight:bold;'>⚠ Already exists — skipped</span>";
                } else {
                    echo "<span style='color:red; font-weight:bold;'>✗ Error: " . htmlspecialchars($msg) . "</span>";
                }
            }
            echo "</div>";
        }

        echo "<hr style='opacity:0.1; margin:20px 0;'>";
        echo "<div style='text-align:center;'>";
        echo "<b style='color:#1a7a4a; font-size:1.2rem;'>✓ Migration Complete!</b><br><br>";
        echo "<a href='?token=$token' style='display:inline-block; padding:10px 20px; background:#4A6372; color:#fff; text-decoration:none; border-radius:6px; margin-right:10px;'>Clear Caches →</a>";
        echo "<a href='/admin/dashboard' style='display:inline-block; padding:10px 20px; background:#1a7a4a; color:#fff; text-decoration:none; border-radius:6px;'>Dashboard →</a>";
        echo "</div>";

    // ─── CACHE CLEAR MODE ──────────────────────────────────────────
    } else {
        echo "<h1 style='color:#4A6372; margin-top:0;'>🧹 Server Cache Cleansing</h1>";
        echo "<hr style='opacity:0.1; margin-bottom:25px;'>";

        $commands = [
            'config:clear'   => 'Configuration Cache',
            'route:clear'    => 'Routes Cache',
            'view:clear'     => 'Views & Templates',
            'cache:clear'    => 'General App Memory',
            'optimize:clear' => 'Optimization Files',
        ];

        foreach ($commands as $command => $label) {
            echo "<div style='margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;'>";
            echo "<span>Clearing <b>$label</b>...</span>";
            Artisan::call($command);
            echo "<span style='color:#1a7a4a; font-weight:bold;'>[ DONE ]</span>";
            echo "</div>";
        }

        echo "<hr style='opacity:0.1; margin:25px 0;'>";
        echo "<div style='text-align:center;'>";
        echo "<b style='color:#1a7a4a; font-size:1.25rem;'>✓ All Caches Cleared!</b>";
        echo "<p style='color:#666;'>Your latest changes are now live.</p>";
        echo "<a href='?token=$token&action=migrate' style='display:inline-block; margin:5px; padding:10px 22px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:6px;'>Run Migrations →</a>";
        echo "<a href='/admin/dashboard' style='display:inline-block; margin:5px; padding:10px 22px; background:#1a7a4a; color:#fff; text-decoration:none; border-radius:6px;'>Dashboard →</a>";
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<b style='color:red;'>Error:</b><br>";
    echo "<pre style='background:#f8f9fa; padding:10px; margin-top:10px;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

echo "</div></body>";
