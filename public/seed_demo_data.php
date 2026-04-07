<?php

/**
 * Standalone PHP script to create default Roles and Dummy Users.
 * This script hashes passwords using BCRYPT (compatible with Laravel).
 * 
 * Instructions:
 * 1. Upload this file to your live server.
 * 2. Visit this file in your browser to run it.
 */

// Using the same credentials you entered in create_tables.php
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

$modelType = 'App\\Models\\User';
$guardName = 'web';
$defaultPassword = password_hash('password', PASSWORD_BCRYPT); // Creates a Laravel-compatible password hash for 'password'

echo "<h4>1. Setting up Roles...</h4>";
$roles = ['admin', 'mufti', 'user'];
foreach ($roles as $role) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$role, $guardName]);
        echo "<p style='color:green'>Role '{$role}' ensured.</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>Setup role '{$role}' failed: " . $e->getMessage() . "</p>";
    }
}

// Fetch the mapping of Role Name -> Role ID so we can assign them correctly
$stmt = $pdo->query("SELECT id, name FROM roles");
$roleIds = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $roleIds[$row['name']] = $row['id'];
}


echo "<h4>2. Creating Dummy Users...</h4>";

$dummyUsers = [
    [
        'name' => 'Super Admin',
        'email' => 'admin@example.com',
        'role' => 'admin'
    ],
    [
        'name' => 'Test Mufti',
        'email' => 'mufti@example.com',
        'role' => 'mufti'
    ],
    [
        'name' => 'Questioner User',
        'email' => 'user@example.com',
        'role' => 'user'
    ],
];

foreach ($dummyUsers as $u) {
    // 1. Insert user if they don't exist
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, profile_completed, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, 1, NOW(), NOW())");
        $stmt->execute([$u['name'], $u['email'], $defaultPassword]);
        
        // Get the ID of the user (whether just inserted or previously inserted)
        $stmtGet = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtGet->execute([$u['email']]);
        $userId = $stmtGet->fetchColumn();

        if ($userId) {
            echo "<p style='color:green'>User {$u['email']} ensured (ID: {$userId}).</p>";
            
            // Assign the role relation
            if (isset($roleIds[$u['role']])) {
                $roleId = $roleIds[$u['role']];
                $stmtRole = $pdo->prepare("INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)");
                $stmtRole->execute([$roleId, $modelType, $userId]);
                echo "<p style='color:blue'>&mdash; Assigned Role '{$u['role']}' to {$u['email']}</p>";
            } else {
                echo "<p style='color:red'>Error: Role {$u['role']} ID not found.</p>";
            }
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Failed setting up user {$u['email']}: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Demo Data Seeding Complete!</h3>";
echo "<p><strong>Your test accounts are:</strong></p>";
echo "<ul>
    <li>Admin: admin@example.com / Password: password</li>
    <li>Mufti: mufti@example.com / Password: password</li>
    <li>User: user@example.com / Password: password</li>
</ul>";

echo "<p style='color: orange;'><strong>SECURITY WARNING:</strong> Please delete this file from your server after running it so visitors cannot recreate admin accounts!</p>";

?>
