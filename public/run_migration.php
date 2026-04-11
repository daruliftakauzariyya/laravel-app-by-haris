<?php
/**
 * Run migration for fatwa_featured_rules table
 * Visit: https://new.daruliftakauzariyya.com/run_migration.php
 */

$host    = '162.214.80.164';
$db      = 'ensplpmy_darulifta-lar-db';
$user    = 'ensplpmy_darulifta-lar-use';
$pass    = 'DaurIfta123@2026';
$pdo     = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

echo "<h2>Migration: fatwa_featured_rules</h2><pre>";

try {
    // Create table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `fatwa_featured_rules` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(200) NOT NULL,
            `hijri_month` tinyint DEFAULT NULL,
            `hijri_day_start` tinyint DEFAULT NULL,
            `hijri_day_end` tinyint DEFAULT NULL,
            `country_codes` json DEFAULT NULL,
            `category_id` int DEFAULT NULL,
            `madhab_id` int DEFAULT NULL,
            `fatwa_ids` json DEFAULT NULL,
            `section_label` varchar(120) NOT NULL DEFAULT 'Most Useful & Readable Fatwas',
            `section_sub` varchar(300) DEFAULT NULL,
            `limit` tinyint NOT NULL DEFAULT '6',
            `priority` int NOT NULL DEFAULT '0',
            `is_active` tinyint(1) NOT NULL DEFAULT '1',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_category_id` (`category_id`),
            KEY `idx_madhab_id` (`madhab_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "✅ Table 'fatwa_featured_rules' created successfully.\n";

    // Insert a default catch-all rule (always active, shows most-viewed)
    $existing = $pdo->query("SELECT COUNT(*) FROM fatwa_featured_rules")->fetchColumn();
    if ($existing == 0) {
        $pdo->exec("
            INSERT INTO `fatwa_featured_rules` (`name`, `section_label`, `section_sub`, `limit`, `priority`, `is_active`, `created_at`, `updated_at`)
            VALUES (
                'Default: Most Popular Fatwas',
                'Most Useful & Readable Fatwas',
                'The most read and appreciated Islamic rulings from our scholars.',
                6, 0, 1, NOW(), NOW()
            )
        ");
        echo "✅ Default rule inserted (catch-all, shows most-viewed fatwas).\n";
    }

    echo "\n✅ Migration complete! You can delete this file.\n";
    echo "\nManage rules at: <a href='https://new.daruliftakauzariyya.com/admin/featured-rules'>Admin → Featured Fatwas</a>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
echo "</pre>";
