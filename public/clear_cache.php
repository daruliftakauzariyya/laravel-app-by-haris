<?php
/**
 * Clear Laravel view cache and application cache
 * Run once at: https://daruliftakauzariyya.com/clear_cache.php
 */

// Clear compiled view cache
$viewPath = __DIR__ . '/../storage/framework/views/';
$cleared = 0;
if (is_dir($viewPath)) {
    foreach (glob($viewPath . '*.php') as $file) {
        if (unlink($file)) $cleared++;
    }
}

// Also attempt artisan cache clearing via exec (if available)
$artisan = __DIR__ . '/../artisan';
if (file_exists($artisan)) {
    @exec('php ' . escapeshellarg($artisan) . ' config:clear 2>&1', $out1);
    @exec('php ' . escapeshellarg($artisan) . ' route:clear 2>&1', $out2);
}

echo "<h2>✅ Cache Cleared</h2>";
echo "<p>Compiled view files removed: <strong>$cleared</strong></p>";
echo "<p>Laravel will recompile all views on next page load.</p>";
echo "<p>Now visit: <a href='https://daruliftakauzariyya.com/mufti/questions'>Mufti Questions</a> to verify the fix.</p>";
