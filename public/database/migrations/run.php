<?php

/**
 * Migration Runner for Live Server
 *
 * Adds missing columns:
 *   - users.mother_language
 *   - supporting_references.sort_order
 *
 * Usage:
 *   https://daruliftakauzariyya.com/database/migrations/run.php?token=migrate_2026
 *
 * Instructions:
 *   1. This file is already in public/database/migrations/run.php
 *   2. Visit the URL above in your browser to run the migration.
 *   3. DELETE or rename this file after use for security.
 */

$token = 'migrate_2026'; // Secret token — change if needed

if (!isset($_GET['token']) || $_GET['token'] !== $token) {
    http_response_code(403);
    die('<b style="color:red;">403 Unauthorized.</b> Provide the correct ?token= to proceed.');
}

// Bootstrap Laravel
require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';

// Boot the console kernel so facades (Schema, Artisan, etc.) are available
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "<h2 style='font-family:sans-serif;'>Darulifta Kauzariyya — Migration Runner</h2>";
    echo "<p style='font-family:sans-serif;'>Bootstrapping Laravel... <b style='color:green;'>Done.</b></p>";

    // ── Pre-flight: show current column status ────────────────────────────────
    $hasMotherLanguage = Schema::hasColumn('users', 'mother_language');
    $hasMuftiSortOrder = Schema::hasColumn('users', 'mufti_sort_order');
    $hasSortOrder      = Schema::hasColumn('supporting_references', 'sort_order');

    echo "<h3 style='font-family:sans-serif;'>Pre-flight Column Check</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='font-family:sans-serif; border-collapse:collapse;'>";
    echo "<tr><th>Table</th><th>Column</th><th>Exists?</th></tr>";
    echo "<tr>
            <td>users</td>
            <td>mother_language</td>
            <td style='color:" . ($hasMotherLanguage ? 'green' : 'red') . ";'>" . ($hasMotherLanguage ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>users</td>
            <td>mufti_sort_order</td>
            <td style='color:" . ($hasMuftiSortOrder ? 'green' : 'red') . ";'>" . ($hasMuftiSortOrder ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>supporting_references</td>
            <td>sort_order</td>
            <td style='color:" . ($hasSortOrder ? 'green' : 'red') . ";'>" . ($hasSortOrder ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "</table><br>";

    // ── Run the migration ─────────────────────────────────────────────────────
    echo "<h3 style='font-family:sans-serif;'>Running Migration...</h3>";

    Artisan::call('migrate', [
        '--path'  => 'database/migrations/2026_06_29_100000_add_missing_columns_to_users_and_supporting_references.php',
        '--force' => true,
    ]);

    $output = Artisan::output();

    echo "<pre style='background:#f4f4f4; padding:15px; border:1px solid #ddd; font-family:monospace;'>"
        . htmlspecialchars($output)
        . "</pre>";

    // Fallback: If Artisan skipped it because the migration timestamp was already recorded,
    // run the schema builder directly for any missing column.
    if (!Schema::hasColumn('users', 'mother_language')) {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mother_language')->nullable()->after('district');
        });
        echo "<p style='color:blue; font-family:sans-serif;'>Applied fallback: users.mother_language column created.</p>";
    }
    if (!Schema::hasColumn('users', 'mufti_sort_order')) {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('mufti_sort_order')->default(0)->after('mother_language');
        });
        echo "<p style='color:blue; font-family:sans-serif;'>Applied fallback: users.mufti_sort_order column created.</p>";
    }
    if (!Schema::hasColumn('supporting_references', 'sort_order')) {
        Schema::table('supporting_references', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('language');
        });
        echo "<p style='color:blue; font-family:sans-serif;'>Applied fallback: supporting_references.sort_order column created.</p>";
    }

    // ── Post-flight: confirm columns now exist ────────────────────────────────
    $hasMotherLanguageNow = Schema::hasColumn('users', 'mother_language');
    $hasMuftiSortOrderNow = Schema::hasColumn('users', 'mufti_sort_order');
    $hasSortOrderNow      = Schema::hasColumn('supporting_references', 'sort_order');

    echo "<h3 style='font-family:sans-serif;'>Post-migration Column Check</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='font-family:sans-serif; border-collapse:collapse;'>";
    echo "<tr><th>Table</th><th>Column</th><th>Status</th></tr>";
    echo "<tr>
            <td>users</td>
            <td>mother_language</td>
            <td style='color:" . ($hasMotherLanguageNow ? 'green' : 'red') . ";'>" . ($hasMotherLanguageNow ? '✔ OK' : '✘ STILL MISSING') . "</td>
          </tr>";
    echo "<tr>
            <td>users</td>
            <td>mufti_sort_order</td>
            <td style='color:" . ($hasMuftiSortOrderNow ? 'green' : 'red') . ";'>" . ($hasMuftiSortOrderNow ? '✔ OK' : '✘ STILL MISSING') . "</td>
          </tr>";
    echo "<tr>
            <td>supporting_references</td>
            <td>sort_order</td>
            <td style='color:" . ($hasSortOrderNow ? 'green' : 'red') . ";'>" . ($hasSortOrderNow ? '✔ OK' : '✘ STILL MISSING') . "</td>
          </tr>";
    echo "</table><br>";

    if ($hasMotherLanguageNow && $hasMuftiSortOrderNow && $hasSortOrderNow) {
        echo "<b style='color:green; font-family:sans-serif; font-size:16px;'>✔ Migration completed successfully! Both columns are now present.</b>";
    } else {
        echo "<b style='color:red; font-family:sans-serif; font-size:16px;'>⚠ Some columns are still missing. Check the output above.</b>";
    }

    echo "<p style='font-family:sans-serif; color:#888; font-size:12px;'>⚠ <b>Security reminder:</b> Delete or rename this file after use.</p>";

} catch (Exception $e) {
    echo "<b style='color:red; font-family:sans-serif;'>Error during migration:</b><br>";
    echo "<pre style='background:#fff0f0; padding:15px; border:1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
