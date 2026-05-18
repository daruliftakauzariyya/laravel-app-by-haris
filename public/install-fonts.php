<?php
/**
 * One-time Font Installer Script
 * Visit: domain.com/install-fonts.php?token=fatwa_deploy_2026
 * DELETE this file after running!
 */
$token = 'fatwa_deploy_2026';
if (!isset($_GET['token']) || $_GET['token'] !== $token) { die('Unauthorized'); }

$src  = __DIR__ . '/../resources/font';
$dest = __DIR__ . '/assets/fonts';

function copyFonts($src, $dest) {
    if (!is_dir($dest)) { mkdir($dest, 0755, true); }
    $dir = opendir($src);
    $log = [];
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') continue;
        $srcPath  = $src  . DIRECTORY_SEPARATOR . $file;
        $destPath = $dest . DIRECTORY_SEPARATOR . $file;
        if (is_dir($srcPath)) {
            $log = array_merge($log, copyFonts($srcPath, $destPath));
        } elseif (in_array(pathinfo($file, PATHINFO_EXTENSION), ['ttf','woff','woff2','otf'])) {
            copy($srcPath, $destPath);
            $log[] = "✅ Copied: $file";
        }
    }
    closedir($dir);
    return $log;
}

$results = copyFonts($src, $dest);

echo "<style>body{font-family:sans-serif; padding:40px;} pre{background:#f4f9f4; padding:15px; border-left:4px solid green;}</style>";
echo "<h2>Font Installation</h2><pre>";
foreach($results as $r) echo $r . "\n";
echo "</pre>";
echo count($results) > 0 
    ? "<p style='color:green; font-weight:bold;'>✅ Done! All font files copied to public/assets/fonts/</p>"
    : "<p style='color:red;'>⚠️ No font files found — check resources/font directory.</p>";
echo "<p><strong>IMPORTANT: Delete this file after use!</strong></p>";
