<?php
// Standalone migration script to bypass Laravel boot issues

$host = '162.214.80.164';
$db   = 'ensplpmy_darulifta-lar-db';
$user = 'ensplpmy_darulifta-lar-use';
$pass = 'DaurIfta123@2026';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new \PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

echo "Connected to database.\n";

// 1. Create madhabs table
echo "Creating madhabs table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS madhabs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
)");

// 2. Create madhab_user table
echo "Creating madhab_user table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS madhab_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    madhab_id INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (user_id),
    INDEX (madhab_id)
)");

// 3. Seed madhabs
echo "Seeding madhabs...\n";
$madhabs = ['Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'General/Other'];
foreach ($madhabs as $name) {
    $slug = strtolower(str_replace([' ', '/'], '-', $name));
    $stmt = $pdo->prepare("INSERT IGNORE INTO madhabs (name, slug, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $stmt->execute([$name, $slug]);
}

// 4. Add madhab_id to questions and users
echo "Adding madhab_id columns...\n";
try { $pdo->exec("ALTER TABLE questions ADD COLUMN madhab_id INT NULL AFTER category_id"); } catch (Exception $e) {}
try { $pdo->exec("ALTER TABLE users ADD COLUMN madhab_id INT NULL AFTER district"); } catch (Exception $e) {}

// 5. Migrate data
echo "Migrating data...\n";
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

// Questions
$questions = $pdo->query("SELECT id, madhab FROM questions")->fetchAll();
foreach ($questions as $q) {
    $mid = $mapping[$q['madhab']] ?? $generalId;
    $pdo->prepare("UPDATE questions SET madhab_id = ? WHERE id = ?")->execute([$mid, $q['id']]);
}

// Users
$users = $pdo->query("SELECT id, madhab FROM users")->fetchAll();
foreach ($users as $u) {
    if (empty($u['madhab'])) continue;

    $mValue = json_decode($u['madhab'], true);
    if (is_array($mValue)) {
        // Muftis
        foreach ($mValue as $mName) {
            $mid = $mapping[$mName] ?? $generalId;
            if ($mid) {
                $pdo->prepare("INSERT IGNORE INTO madhab_user (user_id, madhab_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())")
                    ->execute([$u['id'], $mid]);
            }
        }
    } else {
        // Questioners
        $mid = $mapping[$u['madhab']] ?? $generalId;
        $pdo->prepare("UPDATE users SET madhab_id = ? WHERE id = ?")->execute([$mid, $u['id']]);
    }
}

// 6. Drop old columns
echo "Dropping old columns...\n";
try { $pdo->exec("ALTER TABLE questions DROP COLUMN madhab"); } catch (Exception $e) {}
try { $pdo->exec("ALTER TABLE users DROP COLUMN madhab"); } catch (Exception $e) {}

echo "Migration completed successfully!\n";
