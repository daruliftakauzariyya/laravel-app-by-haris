<?php

$dbConfig = [
    'host' => '162.214.80.164',
    'dbname' => 'ensplpmy_darulifta-lar-db',
    'user' => 'ensplpmy_darulifta-lar-use',
    'pass' => 'DaurIfta123@2026',
];

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4",
        $dbConfig['user'],
        $dbConfig['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<h3>Connected to Database successfully!</h3>";
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

$queries = [
    "ALTER TABLE fatwas ADD COLUMN verified_by BIGINT UNSIGNED NULL AFTER published_by",
    "ALTER TABLE fatwas ADD COLUMN checked_approved_by BIGINT UNSIGNED NULL AFTER verified_by",
    "ALTER TABLE users ADD COLUMN can_check_approve TINYINT(1) DEFAULT 0 AFTER is_active",
];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>✅ Success: " . htmlspecialchars($sql) . "</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color: blue;'>ℹ️ Column already exists, skipping: " . htmlspecialchars($sql) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h3>Database update complete.</h3>";
echo "<p style='color: orange;'><strong>SECURITY WARNING:</strong> Please delete this file from your server after it runs successfully!</p>";

?>
