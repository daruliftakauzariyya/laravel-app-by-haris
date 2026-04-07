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

$sql = "ALTER TABLE users
    ADD COLUMN country_code VARCHAR(255) NULL AFTER country,
    ADD COLUMN address_line_1 TEXT NULL AFTER country_code,
    ADD COLUMN address_line_2 TEXT NULL AFTER address_line_1,
    ADD COLUMN state VARCHAR(255) NULL AFTER address_line_2,
    ADD COLUMN district VARCHAR(255) NULL AFTER state,
    ADD COLUMN madhab ENUM('Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'Other', 'No Preference') NULL AFTER district,
    ADD COLUMN otp VARCHAR(255) NULL AFTER remember_token,
    ADD COLUMN otp_expires_at TIMESTAMP NULL AFTER otp;";

try {
    $pdo->exec($sql);
    echo "<p style='color: green;'>Successfully added 'otp' and all other new columns to the users table!</p>";
} catch (PDOException $e) {
    // MariaDB might not support 'IF NOT EXISTS' for ADD COLUMN in older versions
    // Fallback to catching the duplicate column error
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "<p style='color: green;'>The 'otp' and other columns already exist in the users table!</p>";
    } else {
        echo "<p style='color: red;'>Error updating users table: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Database update complete.</h3>";
echo "<p style='color: orange;'><strong>SECURITY WARNING:</strong> Please delete this file from your server after it runs successfully!</p>";

?>
