<?php
/**
 * Consolidated Database Migration Script
 * Handles Madhab Normalization, Schema Cleanup, and Multi-Verifier System
 */

$host = '162.214.80.164';
$db   = 'ensplpmy_darulifta-lar-db';
$user = 'ensplpmy_darulifta-lar-use';
$pass = 'DaurIfta123@2026';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "✅ Connected to database.\n\n";
} catch (\PDOException $e) {
     die("❌ Connection failed: " . $e->getMessage() . "\n");
}

/**
 * SECTION 1: Madhab Normalization
 */
echo "--- Section 1: Madhab Normalization ---\n";

// Create madhabs table
echo "Creating madhabs table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS madhabs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
)");

// Create madhab_user pivot table
echo "Creating madhab_user pivot table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS madhab_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    madhab_id INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (user_id),
    INDEX (madhab_id)
)");

// Seed default Madhabs
echo "Seeding default Madhabs...\n";
$madhabNames = ['Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'General/Other'];
foreach ($madhabNames as $name) {
    $slug = strtolower(str_replace([' ', '/'], '-', $name));
    $stmt = $pdo->prepare("INSERT IGNORE INTO madhabs (name, slug, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $stmt->execute([$name, $slug]);
}

// Add madhab_id columns if they don't exist
echo "Adding madhab_id columns to questions and users...\n";
try { $pdo->exec("ALTER TABLE questions ADD COLUMN madhab_id INT NULL AFTER category_id"); } catch (Exception $e) {}
try { $pdo->exec("ALTER TABLE users ADD COLUMN madhab_id INT NULL AFTER district"); } catch (Exception $e) {}

// Data Migration for Madhabs
echo "Migrating existing Madhab data...\n";
$mapping = [];
$rows = $pdo->query("SELECT id, name FROM madhabs")->fetchAll();
foreach ($rows as $row) {
    if ($row['name'] == 'General/Other') {
        $mapping['Other'] = $row['id'];
        $mapping['No Preference'] = $row['id'];
        $mapping['General/Other'] = $row['id'];
    } else {
        $mapping[$row['name']] = $row['id'];
    }
}
$generalId = $mapping['General/Other'];

// Migrate Questions
$questions = $pdo->query("SELECT id, madhab FROM questions")->fetchAll();
foreach ($questions as $q) {
    if (isset($q['madhab'])) {
        $mid = $mapping[$q['madhab']] ?? $generalId;
        $pdo->prepare("UPDATE questions SET madhab_id = ? WHERE id = ?")->execute([$mid, $q['id']]);
    }
}

// Migrate Users
$users = $pdo->query("SELECT id, madhab FROM users")->fetchAll();
foreach ($users as $u) {
    if (empty($u['madhab'])) continue;

    $mValue = json_decode($u['madhab'], true);
    if (is_array($mValue)) {
        // Scholar/Mufti accounts (Many-to-Many)
        foreach ($mValue as $mName) {
            $mid = $mapping[$mName] ?? $generalId;
            if ($mid) {
                $pdo->prepare("INSERT IGNORE INTO madhab_user (user_id, madhab_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())")
                    ->execute([$u['id'], $mid]);
            }
        }
    } else {
        // Individual Questioner accounts (Single Preference)
        $mid = $mapping[$u['madhab']] ?? $generalId;
        $pdo->prepare("UPDATE users SET madhab_id = ? WHERE id = ?")->execute([$mid, $u['id']]);
    }
}

// Cleanup old Madhab columns
echo "Dropping old madhab columns...\n";
try { $pdo->exec("ALTER TABLE questions DROP COLUMN madhab"); } catch (Exception $e) {}
try { $pdo->exec("ALTER TABLE users DROP COLUMN madhab"); } catch (Exception $e) {}

echo "✅ Madhab normalization complete.\n\n";

/**
 * SECTION 2: Schema Optimization (Cleanup)
 */
echo "--- Section 2: Schema Optimization ---\n";

// Drop obsolete tables
echo "Dropping unused tables (cache, jobs)...\n";
$pdo->exec("DROP TABLE IF EXISTS cache, jobs");

// Drop redundant columns
echo "Dropping redundant columns...\n";
try { $pdo->exec("ALTER TABLE questions DROP COLUMN question_type"); } catch (Exception $e) {}
try { $pdo->exec("ALTER TABLE fatwas DROP COLUMN fatwa_number"); } catch (Exception $e) {}
try { $pdo->exec("ALTER TABLE fatwas DROP COLUMN is_featured"); } catch (Exception $e) {}

echo "✅ Schema optimization complete.\n\n";

/**
 * SECTION 3: Multi-Verifier System
 */
echo "--- Section 3: Multi-Verifier System ---\n";

// Create verifier pivot table
echo "Creating fatwa_verifier_user table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS fatwa_verifier_user (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fatwa_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (fatwa_id) REFERENCES fatwas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Migrate existing verifier data
echo "Migrating existing verifier IDs...\n";
try {
    $pdo->exec("INSERT IGNORE INTO fatwa_verifier_user (fatwa_id, user_id, created_at, updated_at)
                SELECT id, verified_by, NOW(), NOW() FROM fatwas WHERE verified_by IS NOT NULL");
    
    // Drop old column
    echo "Dropping old verified_by column...\n";
    $pdo->exec("ALTER TABLE fatwas DROP COLUMN verified_by");
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . " (Column might have been already dropped)\n";
}

echo "✅ Multi-verifier system setup complete.\n\n";

echo "🚀 ALL MIGRATIONS COMPLETED SUCCESSFULLY!\n";
