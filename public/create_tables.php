<?php

/**
 * Standalone PHP script to create all tables and columns based on your Laravel migrations.
 * This script uses pure PDO and standard SQL 'CREATE TABLE IF NOT EXISTS'.
 * 
 * Instructions:
 * 1. Upload this file to your live server.
 * 2. Update the $dbConfig values with your live server's database credentials.
 * 3. Visit this file in your browser to run it.
 */

$dbConfig = [
    'host' => '162.214.80.164', // Usually localhost
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

// Ensure strict mode is turned off for easier imports, though optional
$pdo->exec("SET FOREIGN_KEY_CHECKS=0;");

$tables = [];

// 1. Users Table
$tables['users'] = "CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NULL,
    google_id VARCHAR(255) NULL UNIQUE,
    avatar VARCHAR(255) NULL,
    gender VARCHAR(255) NULL,
    country VARCHAR(255) NULL,
    country_code VARCHAR(255) NULL,
    mobile VARCHAR(255) NULL,
    address_line_1 TEXT NULL,
    address_line_2 TEXT NULL,
    state VARCHAR(255) NULL,
    district VARCHAR(255) NULL,
    madhab ENUM('Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'Other', 'No Preference') NULL,
    mother_language VARCHAR(255) NULL,
    profile_completed TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    otp VARCHAR(255) NULL,
    otp_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 2. Cache tables
$tables['cache'] = "CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    INDEX(expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 3. Jobs tables
$tables['jobs'] = "CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX(queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 4. Categories
$tables['categories'] = "CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255) NULL,
    name_ur VARCHAR(255) NULL,
    parent_id BIGINT UNSIGNED NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 5. Questions
$tables['questions'] = "CREATE TABLE IF NOT EXISTS questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id VARCHAR(255) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    assigned_mufti_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NULL,
    body TEXT NOT NULL,
    madhab ENUM('Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'Other') DEFAULT 'Hanafi',
    language VARCHAR(255) DEFAULT 'English',
    question_type VARCHAR(255) NULL,
    status ENUM('pending', 'accepted', 'assigned', 'answered', 'under_review', 'approved', 'rejected', 'published') DEFAULT 'pending',
    accepted_at TIMESTAMP NULL,
    assigned_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_mufti_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 6. Question Files
$tables['question_files'] = "CREATE TABLE IF NOT EXISTS question_files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_size BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 7. Mufti Answers
$tables['mufti_answers'] = "CREATE TABLE IF NOT EXISTS mufti_answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    mufti_id BIGINT UNSIGNED NOT NULL,
    summary TEXT NOT NULL,
    full_answer LONGTEXT NOT NULL,
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'correction_requested') DEFAULT 'draft',
    admin_notes TEXT NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    submitted_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (mufti_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 8. Supporting References
$tables['supporting_references'] = "CREATE TABLE IF NOT EXISTS supporting_references (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mufti_answer_id BIGINT UNSIGNED NOT NULL,
    arabic_text LONGTEXT NOT NULL,
    book_name VARCHAR(255) NOT NULL,
    language VARCHAR(255) DEFAULT 'Arabic',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (mufti_answer_id) REFERENCES mufti_answers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 9. Fatwas
$tables['fatwas'] = "CREATE TABLE IF NOT EXISTS fatwas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    mufti_answer_id BIGINT UNSIGNED NOT NULL,
    published_by BIGINT UNSIGNED NOT NULL,
    fatwa_number VARCHAR(255) NULL UNIQUE,
    is_featured TINYINT(1) DEFAULT 0,
    views BIGINT UNSIGNED DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (mufti_answer_id) REFERENCES mufti_answers(id) ON DELETE CASCADE,
    FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 10. Roles & Permissions (Spatie Structure)
$tables['permissions'] = "CREATE TABLE IF NOT EXISTS permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$tables['roles'] = "CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$tables['model_has_permissions'] = "CREATE TABLE IF NOT EXISTS model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    INDEX model_has_permissions_model_id_model_type_index (model_id, model_type),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$tables['model_has_roles'] = "CREATE TABLE IF NOT EXISTS model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    INDEX model_has_roles_model_id_model_type_index (model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$tables['role_has_permissions'] = "CREATE TABLE IF NOT EXISTS role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";


// Execute Queries
foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>Table <strong>{$name}</strong> created or already exists.</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error creating table <strong>{$name}</strong>: " . $e->getMessage() . "</p>";
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
echo "<h3>Database setup process complete!</h3>";

// Delete this file after running
echo "<p style='color: orange;'><strong>SECURITY WARNING:</strong> Please delete this file from your server after it runs successfully!</p>";

?>