<?php

/**
 * Darul Ifta Portal — Database Migration Utility
 * 
 * Allows triggering migrations via web browser with a token.
 * Useful in shared hosting environments where CLI access is restricted.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

/**
 * Check if a specific index exists on a table
 */
function helper_index_exists($table, $indexName) {
    try {
        $conn = Schema::getConnection();
        if ($conn->getDriverName() === 'sqlite') {
            $results = $conn->select("PRAGMA index_list(`{$table}`);");
            foreach ($results as $row) {
                if ($row->name === $indexName) return true;
            }
            return false;
        }
        $results = $conn->select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($results) > 0;
    } catch (\Exception $e) {
        return false;
    }
}

try {
    echo "<h2 style='font-family:sans-serif;'>Darulifta Kauzariyya — Migration Runner</h2>";
    echo "<p style='font-family:sans-serif;'>Bootstrapping Laravel... <b style='color:green;'>Done.</b></p>";

    // ── Pre-flight Check ──────────────────────────────────────────────────────
    $hasMotherLanguage = Schema::hasColumn('users', 'mother_language');
    $hasMuftiSortOrder = Schema::hasColumn('users', 'mufti_sort_order');
    $hasSortOrder      = Schema::hasColumn('supporting_references', 'sort_order');

    $hasStatusIdx      = helper_index_exists('questions', 'idx_questions_status');
    $hasPublishedAtIdx = helper_index_exists('fatwas', 'idx_fatwas_published_at');
    $hasViewsIdx       = helper_index_exists('fatwas', 'idx_fatwas_views');
    $hasLikesIdx       = helper_index_exists('fatwas', 'idx_fatwas_likes');

    echo "<h3 style='font-family:sans-serif;'>Pre-flight Column & Index Check</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='font-family:sans-serif; border-collapse:collapse; min-width:400px;'>";
    echo "<tr><th>Table</th><th>Item (Column/Index)</th><th>Exists?</th></tr>";
    echo "<tr>
            <td>users</td>
            <td>mother_language (column)</td>
            <td style='color:" . ($hasMotherLanguage ? 'green' : 'red') . ";'>" . ($hasMotherLanguage ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>users</td>
            <td>mufti_sort_order (column)</td>
            <td style='color:" . ($hasMuftiSortOrder ? 'green' : 'red') . ";'>" . ($hasMuftiSortOrder ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>supporting_references</td>
            <td>sort_order (column)</td>
            <td style='color:" . ($hasSortOrder ? 'green' : 'red') . ";'>" . ($hasSortOrder ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>questions</td>
            <td>idx_questions_status (index)</td>
            <td style='color:" . ($hasStatusIdx ? 'green' : 'red') . ";'>" . ($hasStatusIdx ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>fatwas</td>
            <td>idx_fatwas_published_at (index)</td>
            <td style='color:" . ($hasPublishedAtIdx ? 'green' : 'red') . ";'>" . ($hasPublishedAtIdx ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>fatwas</td>
            <td>idx_fatwas_views (index)</td>
            <td style='color:" . ($hasViewsIdx ? 'green' : 'red') . ";'>" . ($hasViewsIdx ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "<tr>
            <td>fatwas</td>
            <td>idx_fatwas_likes (index)</td>
            <td style='color:" . ($hasLikesIdx ? 'green' : 'red') . ";'>" . ($hasLikesIdx ? '✔ YES' : '✘ NO — will be added') . "</td>
          </tr>";
    echo "</table><br>";

    // ── Run Migration 1 (Columns) ─────────────────────────────────────────────
    echo "<h3 style='font-family:sans-serif;'>Running Columns Migration...</h3>";
    Artisan::call('migrate', [
        '--path'  => 'database/migrations/2026_06_29_100000_add_missing_columns_to_users_and_supporting_references.php',
        '--force' => true,
    ]);
    echo "<pre style='background:#f4f4f4; padding:15px; border:1px solid #ddd; font-family:monospace;'>"
        . htmlspecialchars(Artisan::output())
        . "</pre>";

    // ── Run Migration 2 (Performance Indexes) ──────────────────────────────────
    echo "<h3 style='font-family:sans-serif;'>Running Performance Indexes Migration...</h3>";
    Artisan::call('migrate', [
        '--path'  => 'database/migrations/2026_06_29_110000_add_performance_indexes_to_questions_and_fatwas_tables.php',
        '--force' => true,
    ]);
    echo "<pre style='background:#f4f4f4; padding:15px; border:1px solid #ddd; font-family:monospace;'>"
        . htmlspecialchars(Artisan::output())
        . "</pre>";

    // ── Fallbacks: Columns ────────────────────────────────────────────────────
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

    // ── Fallbacks: Indexes ────────────────────────────────────────────────────
    if (!helper_index_exists('questions', 'idx_questions_status')) {
        try {
            Schema::table('questions', function (Blueprint $table) {
                $table->index('status', 'idx_questions_status');
            });
            echo "<p style='color:blue; font-family:sans-serif;'>Applied fallback: questions.status index created.</p>";
        } catch (\Exception $e) {
            echo "<p style='color:red; font-family:sans-serif;'>Fallback index error: " . $e->getMessage() . "</p>";
        }
    }
    if (!helper_index_exists('fatwas', 'idx_fatwas_published_at')) {
        try {
            Schema::table('fatwas', function (Blueprint $table) {
                $table->index('published_at', 'idx_fatwas_published_at');
            });
            echo "<p style='color:blue; font-family:sans-serif;'>Applied fallback: fatwas.published_at index created.</p>";
        } catch (\Exception $e) {
            echo "<p style='color:red; font-family:sans-serif;'>Fallback index error: " . $e->getMessage() . "</p>";
        }
    }
    if (!helper_index_exists('fatwas', 'idx_fatwas_views')) {
        try {
            Schema::table('fatwas', function (Blueprint $table) {
                $table->index('views', 'idx_fatwas_views');
            });
            echo "<p style='color:blue; font-family:sans-serif;'>Applied fallback: fatwas.views index created.</p>";
        } catch (\Exception $e) {
            echo "<p style='color:red; font-family:sans-serif;'>Fallback index error: " . $e->getMessage() . "</p>";
        }
    }
    if (!helper_index_exists('fatwas', 'idx_fatwas_likes')) {
        try {
            Schema::table('fatwas', function (Blueprint $table) {
                $table->index('likes', 'idx_fatwas_likes');
            });
            echo "<p style='color:blue; font-family:sans-serif;'>Applied fallback: fatwas.likes index created.</p>";
        } catch (\Exception $e) {
            echo "<p style='color:red; font-family:sans-serif;'>Fallback index error: " . $e->getMessage() . "</p>";
        }
    }

    // ── Post-flight Check ─────────────────────────────────────────────────────
    $hasMotherLanguageNow = Schema::hasColumn('users', 'mother_language');
    $hasMuftiSortOrderNow = Schema::hasColumn('users', 'mufti_sort_order');
    $hasSortOrderNow      = Schema::hasColumn('supporting_references', 'sort_order');

    $hasStatusIdxNow      = helper_index_exists('questions', 'idx_questions_status');
    $hasPublishedAtIdxNow = helper_index_exists('fatwas', 'idx_fatwas_published_at');
    $hasViewsIdxNow       = helper_index_exists('fatwas', 'idx_fatwas_views');
    $hasLikesIdxNow       = helper_index_exists('fatwas', 'idx_fatwas_likes');

    echo "<h3 style='font-family:sans-serif;'>Post-migration Status</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='font-family:sans-serif; border-collapse:collapse; min-width:400px;'>";
    echo "<tr><th>Table</th><th>Item (Column/Index)</th><th>Status</th></tr>";
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
    echo "<tr>
            <td>questions</td>
            <td>idx_questions_status</td>
            <td style='color:" . ($hasStatusIdxNow ? 'green' : 'red') . ";'>" . ($hasStatusIdxNow ? '✔ OK' : '✘ STILL MISSING') . "</td>
          </tr>";
    echo "<tr>
            <td>fatwas</td>
            <td>idx_fatwas_published_at</td>
            <td style='color:" . ($hasPublishedAtIdxNow ? 'green' : 'red') . ";'>" . ($hasPublishedAtIdxNow ? '✔ OK' : '✘ STILL MISSING') . "</td>
          </tr>";
    echo "<tr>
            <td>fatwas</td>
            <td>idx_fatwas_views</td>
            <td style='color:" . ($hasViewsIdxNow ? 'green' : 'red') . ";'>" . ($hasViewsIdxNow ? '✔ OK' : '✘ STILL MISSING') . "</td>
          </tr>";
    echo "<tr>
            <td>fatwas</td>
            <td>idx_fatwas_likes</td>
            <td style='color:" . ($hasLikesIdxNow ? 'green' : 'red') . ";'>" . ($hasLikesIdxNow ? '✔ OK' : '✘ STILL MISSING') . "</td>
          </tr>";
    echo "</table><br>";

    $allColumnsOk = $hasMotherLanguageNow && $hasMuftiSortOrderNow && $hasSortOrderNow;
    $allIndexesOk = $hasStatusIdxNow && $hasPublishedAtIdxNow && $hasViewsIdxNow && $hasLikesIdxNow;

    if ($allColumnsOk && $allIndexesOk) {
        echo "<b style='color:green; font-family:sans-serif; font-size:16px;'>✔ All migrations and fallback applications completed successfully! Columns and indexes are active.</b>";
    } else {
        echo "<b style='color:red; font-family:sans-serif; font-size:16px;'>⚠ Some items are still missing. Verify outputs above.</b>";
    }

    echo "<p style='font-family:sans-serif; color:#888; font-size:12px;'>⚠ <b>Security reminder:</b> Delete or rename this file after use.</p>";

} catch (Exception $e) {
    echo "<b style='color:red; font-family:sans-serif;'>Error during migration:</b><br>";
    echo "<pre style='background:#fff0f0; padding:15px; border:1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
