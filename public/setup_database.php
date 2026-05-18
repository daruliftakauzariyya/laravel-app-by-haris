<?php
/**
 * Standalone Database Installer & Seeder
 * 
 * This script consolidates all schema tables creation and default seeds
 * for Darulifta Kauzariyya application.
 * 
 * Features:
 * - Detects CLI / Browser access.
 * - Auto-loads database configuration from .env file.
 * - Creates all tables with correct foreign keys in proper order.
 * - Seeds default roles (Spatie format), users, madhabs, and fully nested categories.
 * 
 * Instructions:
 * 1. Visit from web browser: https://yourdomain.com/setup_database.php
 *    Or run from terminal: php public/setup_database.php
 * 2. Delete this file after successful setup for security!
 */

// Define absolute path helper
define('BASE_PATH', dirname(__DIR__));

// Start Session for browser CSRF/Status tracking
if (php_sapi_name() !== 'cli') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// 1. Parse Database config from Laravel .env
function get_db_credentials() {
    $envPath = BASE_PATH . '/.env';
    
    // Default Fallbacks
    $config = [
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => '',
        'username' => '',
        'password' => ''
    ];
    
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1], " \t\n\r\0\x0B\"'");
                
                if ($key === 'DB_HOST') $config['host'] = $value;
                if ($key === 'DB_PORT') $config['port'] = $value;
                if ($key === 'DB_DATABASE') $config['database'] = $value;
                if ($key === 'DB_USERNAME') $config['username'] = $value;
                if ($key === 'DB_PASSWORD') $config['password'] = $value;
            }
        }
    }
    return $config;
}

// Helper to slugify category names
function make_slug($text) {
    if (empty($text)) return '';
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

// Get credentials
$dbConfig = get_db_credentials();
$isCli = (php_sapi_name() === 'cli');

$status = [];
$error = null;
$pdo = null;

// Handle actual execution request
if ($isCli || (isset($_POST['action']) && $_POST['action'] === 'install')) {
    try {
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        
        $status[] = ["Connected to database successfully.", "success"];
        
        // --- STEP 1: DROP TABLES IN CORRECT ORDER FOR RE-RUNS ---
        if (isset($_POST['fresh']) || $isCli) {
            $status[] = ["Cleaning existing tables...", "info"];
            
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            
            $tablesToDrop = [
                'fatwa_verifier_user', 'fatwa_user_views', 'fatwa_activity_logs', 
                'portal_visitor_logs', 'fatwa_likes', 'fatwa_featured_rules', 
                'supporting_references', 'fatwas', 'mufti_answers', 
                'question_files', 'questions', 'madhab_user', 'madhabs', 
                'categories', 'role_has_permissions', 'model_has_roles', 
                'model_has_permissions', 'roles', 'permissions', 
                'sessions', 'password_reset_tokens', 'users', 'site_settings'
            ];
            
            foreach ($tablesToDrop as $table) {
                $pdo->exec("DROP TABLE IF EXISTS `$table` CASCADE;");
            }
            
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $status[] = ["Database cleared successfully.", "success"];
        }

        // --- STEP 2: CREATE TABLES ---
        $status[] = ["Creating database schema...", "info"];

        // users
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NULL,
            google_id VARCHAR(255) UNIQUE NULL,
            avatar VARCHAR(255) NULL,
            gender VARCHAR(255) NULL,
            country VARCHAR(255) NULL,
            mobile VARCHAR(255) NULL,
            country_code VARCHAR(255) NULL,
            address_line_1 TEXT NULL,
            address_line_2 TEXT NULL,
            state VARCHAR(255) NULL,
            district VARCHAR(255) NULL,
            madhab_id INT NULL,
            profile_completed TINYINT(1) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            can_check_approve TINYINT(1) DEFAULT 0,
            otp VARCHAR(255) NULL,
            otp_expires_at TIMESTAMP NULL,
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // password_reset_tokens
        $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
            email VARCHAR(255) PRIMARY KEY,
            token VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // sessions
        $pdo->exec("CREATE TABLE IF NOT EXISTS sessions (
            id VARCHAR(255) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            payload LONGTEXT NOT NULL,
            last_activity INT NOT NULL,
            INDEX (user_id),
            INDEX (last_activity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Spatie: permissions
        $pdo->exec("CREATE TABLE IF NOT EXISTS permissions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Spatie: roles
        $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Spatie pivots
        $pdo->exec("CREATE TABLE IF NOT EXISTS model_has_permissions (
            permission_id BIGINT UNSIGNED NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (permission_id, model_id, model_type),
            INDEX model_has_permissions_model_id_model_type_index (model_id, model_type),
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS model_has_roles (
            role_id BIGINT UNSIGNED NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (role_id, model_id, model_type),
            INDEX model_has_roles_model_id_model_type_index (model_id, model_type),
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS role_has_permissions (
            permission_id BIGINT UNSIGNED NOT NULL,
            role_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (permission_id, role_id),
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // categories
        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            name_ar VARCHAR(255) NULL,
            name_ur VARCHAR(255) NULL,
            parent_id BIGINT UNSIGNED NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // madhabs
        $pdo->exec("CREATE TABLE IF NOT EXISTS madhabs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) UNIQUE NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Link users to madhabs with a foreign key constraint
        $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_users_madhab_id FOREIGN KEY (madhab_id) REFERENCES madhabs(id) ON DELETE SET NULL;");

        // madhab_user
        $pdo->exec("CREATE TABLE IF NOT EXISTS madhab_user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            madhab_id INT NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (user_id),
            INDEX (madhab_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (madhab_id) REFERENCES madhabs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // questions
        $pdo->exec("CREATE TABLE IF NOT EXISTS questions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question_id VARCHAR(255) UNIQUE NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            category_id BIGINT UNSIGNED NULL,
            madhab_id INT NULL,
            assigned_mufti_id BIGINT UNSIGNED NULL,
            title VARCHAR(255) NULL,
            body TEXT NOT NULL,
            language VARCHAR(255) DEFAULT 'English',
            status ENUM('pending', 'accepted', 'assigned', 'answered', 'under_review', 'approved', 'rejected', 'published') DEFAULT 'pending',
            accepted_at TIMESTAMP NULL,
            assigned_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            FOREIGN KEY (madhab_id) REFERENCES madhabs(id) ON DELETE SET NULL,
            FOREIGN KEY (assigned_mufti_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // question_files
        $pdo->exec("CREATE TABLE IF NOT EXISTS question_files (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question_id BIGINT UNSIGNED NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            file_type VARCHAR(255) NULL,
            file_name VARCHAR(255) NULL,
            file_size INT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // mufti_answers
        $pdo->exec("CREATE TABLE IF NOT EXISTS mufti_answers (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // supporting_references
        $pdo->exec("CREATE TABLE IF NOT EXISTS supporting_references (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            mufti_answer_id BIGINT UNSIGNED NOT NULL,
            book_name VARCHAR(255) NOT NULL,
            arabic_text TEXT NOT NULL,
            language VARCHAR(255) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (mufti_answer_id) REFERENCES mufti_answers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // fatwas
        $pdo->exec("CREATE TABLE IF NOT EXISTS fatwas (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question_id BIGINT UNSIGNED NOT NULL,
            mufti_answer_id BIGINT UNSIGNED NOT NULL,
            published_by BIGINT UNSIGNED NOT NULL,
            checked_approved_by BIGINT UNSIGNED NULL,
            featured_image VARCHAR(255) NULL,
            views BIGINT UNSIGNED DEFAULT 0,
            likes INT DEFAULT 0,
            stars INT DEFAULT 0,
            published_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
            FOREIGN KEY (mufti_answer_id) REFERENCES mufti_answers(id) ON DELETE CASCADE,
            FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (checked_approved_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // fatwa_verifier_user
        $pdo->exec("CREATE TABLE IF NOT EXISTS fatwa_verifier_user (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            fatwa_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (fatwa_id) REFERENCES fatwas(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // fatwa_user_views
        $pdo->exec("CREATE TABLE IF NOT EXISTS fatwa_user_views (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            fatwa_id BIGINT UNSIGNED NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (fatwa_id) REFERENCES fatwas(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // fatwa_activity_logs
        $pdo->exec("CREATE TABLE IF NOT EXISTS fatwa_activity_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question_id BIGINT UNSIGNED NOT NULL,
            fatwa_id BIGINT UNSIGNED NULL,
            user_id BIGINT UNSIGNED NULL,
            event VARCHAR(255) NOT NULL,
            event_label VARCHAR(255) NOT NULL,
            notes TEXT NULL,
            icon VARCHAR(255) DEFAULT 'bi-circle',
            color VARCHAR(50) DEFAULT 'secondary',
            event_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
            FOREIGN KEY (fatwa_id) REFERENCES fatwas(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // portal_visitor_logs
        $pdo->exec("CREATE TABLE IF NOT EXISTS portal_visitor_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NULL,
            country VARCHAR(255) NULL,
            country_code VARCHAR(5) NULL,
            region VARCHAR(255) NULL,
            city VARCHAR(255) NULL,
            page VARCHAR(255) NULL,
            fatwa_id BIGINT UNSIGNED NULL,
            user_id BIGINT UNSIGNED NULL,
            user_agent VARCHAR(500) NULL,
            device_type VARCHAR(255) NULL,
            operating_system VARCHAR(255) NULL,
            browser VARCHAR(255) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (country),
            INDEX (country_code),
            INDEX (created_at),
            FOREIGN KEY (fatwa_id) REFERENCES fatwas(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // site_settings
        $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(255) UNIQUE NOT NULL,
            `value` LONGTEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // fatwa_likes
        $pdo->exec("CREATE TABLE IF NOT EXISTS fatwa_likes (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            fatwa_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NULL,
            ip_address VARCHAR(45) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (fatwa_id) REFERENCES fatwas(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // fatwa_featured_rules
        $pdo->exec("CREATE TABLE IF NOT EXISTS fatwa_featured_rules (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            hijri_month TINYINT NULL,
            hijri_day_start TINYINT NULL,
            hijri_day_end TINYINT NULL,
            country_codes JSON NULL,
            category_id BIGINT UNSIGNED NULL,
            madhab_id INT NULL,
            fatwa_ids JSON NULL,
            section_label VARCHAR(255) NOT NULL DEFAULT 'Most Useful & Readable Fatwas',
            section_sub VARCHAR(500) NULL,
            `limit` TINYINT NOT NULL DEFAULT 6,
            priority INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            FOREIGN KEY (madhab_id) REFERENCES madhabs(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $status[] = ["All database schema tables created successfully.", "success"];

        // --- STEP 3: SEEDING DATA ---
        $status[] = ["Seeding default datasets...", "info"];

        // A. Seed Madhabs
        $madhabs = ['Hanafi', 'Shafii', 'Maliki', 'Hanbali', 'General/Other'];
        $madhabIds = [];
        foreach ($madhabs as $name) {
            $slug = strtolower(str_replace([' ', '/'], '-', $name));
            $stmt = $pdo->prepare("INSERT IGNORE INTO madhabs (name, slug, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([$name, $slug]);
            
            $check = $pdo->prepare("SELECT id FROM madhabs WHERE slug = ?");
            $check->execute([$slug]);
            $madhabIds[$name] = $check->fetchColumn();
        }
        $status[] = ["Seeded default Madhabs successfully.", "success"];

        // B. Seed Spatie Roles
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'mufti', 'guard_name' => 'web'],
            ['name' => 'questioner', 'guard_name' => 'web']
        ];
        $roleIds = [];
        foreach ($roles as $role) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([$role['name'], $role['guard_name']]);
            
            $check = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
            $check->execute([$role['name']]);
            $roleIds[$role['name']] = $check->fetchColumn();
        }
        $status[] = ["Seeded default system Roles successfully.", "success"];

        // C. Seed Users (Admin, Muftis)
        $defaultUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@daruliftakauzariyya.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'admin',
                'can_check_approve' => 0,
                'madhab' => 'General/Other'
            ],
            [
                'name' => 'test mufti 02',
                'email' => 'mufti2@daruliftakauzariyya.com',
                'password' => password_hash('mufti123', PASSWORD_BCRYPT),
                'role' => 'mufti',
                'can_check_approve' => 1,
                'madhab' => 'Hanafi'
            ],
            [
                'name' => 'test mufti 03',
                'email' => 'mufti3@daruliftakauzariyya.com',
                'password' => password_hash('mufti123', PASSWORD_BCRYPT),
                'role' => 'mufti',
                'can_check_approve' => 1,
                'madhab' => 'Hanafi'
            ]
        ];

        foreach ($defaultUsers as $u) {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$u['email']]);
            $uid = $stmt->fetchColumn();
            
            if (!$uid) {
                $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password, is_active, can_check_approve, profile_completed, created_at, updated_at) VALUES (?, ?, ?, 1, ?, 1, NOW(), NOW())");
                $stmtInsert->execute([$u['name'], $u['email'], $u['password'], $u['can_check_approve']]);
                $uid = $pdo->lastInsertId();
                $status[] = ["Created User: <strong>{$u['email']}</strong> (Password: <code>" . ($u['role'] === 'admin' ? 'admin123' : 'mufti123') . "</code>)", "success"];
            } else {
                $status[] = ["User <strong>{$u['email']}</strong> already exists.", "info"];
            }

            // Assign Spatie Role
            $roleId = $roleIds[$u['role']];
            $stmtRole = $pdo->prepare("INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id) VALUES (?, 'App\\\\Models\\\\User', ?)");
            $stmtRole->execute([$roleId, $uid]);

            // Assign Madhab
            $madhabId = $madhabIds[$u['madhab']];
            $stmtMadhab = $pdo->prepare("INSERT IGNORE INTO madhab_user (user_id, madhab_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmtMadhab->execute([$uid, $madhabId]);
        }

        // D. Seed Categories
        $categories = [
            [
                'name' => 'Faiths & Beliefs',
                'name_ar' => 'عقائد وإيمانيات',
                'name_ur' => 'عقائد و ایمانیات',
                'sub' => [
                    ['name' => 'Islamic Beliefs', 'name_ar' => 'العقائد الإسلامية', 'name_ur' => 'اسلامی عقائد'],
                    ['name' => 'World Religions', 'name_ar' => 'الأديان العالمية', 'name_ur' => 'عالمی مذاہب'],
                    ['name' => 'False Sects', 'name_ar' => 'الفرق الباطلة', 'name_ur' => 'باطل فرقے'],
                    ['name' => 'Deviant Sects', 'name_ar' => 'الفرق المنحرفة', 'name_ur' => 'منحرف فرقے'],
                    ['name' => 'Innovations & Customs', 'name_ar' => 'البدع والرسوم', 'name_ur' => 'بدعات و رسومات'],
                    ['name' => 'Taqleed & Fiqhi Schools', 'name_ar' => 'التقليد والمذاهب الفقهية', 'name_ur' => 'تقلید و فقہی مذاہب'],
                    ['name' => 'The Holy Quran', 'name_ar' => 'القرآن الكريم', 'name_ur' => 'قرآن کریم'],
                    ['name' => 'Hadith & Sunnah', 'name_ar' => 'الحديث والسنة', 'name_ur' => 'حدیث و سنت'],
                    ['name' => 'Dawah & Tableeg', 'name_ar' => 'الدعوة والتبليغ', 'name_ur' => 'دعوت و تبلیغ'],
                ]
            ],
            [
                'name' => 'Prayers & Duties',
                'name_ar' => 'عبادات وفرائض',
                'name_ur' => 'عبادات و فرائض',
                'sub' => [
                    ['name' => 'Taharah (Purity)', 'name_ar' => 'الطهارة', 'name_ur' => 'طہارت'],
                    ['name' => 'Salah (Prayer)', 'name_ar' => 'الصلاة', 'name_ur' => 'نماز'],
                    ['name' => 'Jumuah & Eid Prayers', 'name_ar' => 'صلاة الجمعة والعيدين', 'name_ur' => 'جمعہ اور عیدین'],
                    ['name' => 'Death & Funeral', 'name_ar' => 'الموت والجنائز', 'name_ur' => 'موت و جنائز'],
                    ['name' => 'Sawm (Fasting)', 'name_ar' => 'الصوم', 'name_ur' => 'روزہ'],
                    ['name' => 'Zakat & Charity', 'name_ar' => 'الزكاة والصدقات', 'name_ur' => 'زکوۃ و صدقات'],
                    ['name' => 'Hajj & Umrah', 'name_ar' => 'الحج والعمرة', 'name_ur' => 'حج و عمرہ'],
                    ['name' => 'Oaths & Vows', 'name_ar' => 'الأيمان والنذور', 'name_ur' => 'قسمیں اور نذریں'],
                    ['name' => 'Waqf, Mosque, Madrasa', 'name_ar' => 'الوقف والمسجد والمدرسة', 'name_ur' => 'وقف، مسجد اور مدرسہ'],
                    ['name' => 'Qurbani (Slaughtering)', 'name_ar' => 'الأضحية والذبح', 'name_ur' => 'قربانی اور ذبح'],
                ]
            ],
            [
                'name' => 'Social Matters',
                'name_ar' => 'معاملات اجتماعية',
                'name_ur' => 'سماجی معاملات',
                'sub' => [
                    ['name' => 'Nikah (Marriage)', 'name_ar' => 'النكاح', 'name_ur' => 'نکاح'],
                    ['name' => 'Talaq (Divorce)', 'name_ar' => 'الطلاق', 'name_ur' => 'طلاق'],
                    ['name' => 'Food & Drinks', 'name_ar' => 'الأطعمة والأشربة', 'name_ur' => 'کھانے پینے کی چیزیں'],
                    ['name' => 'Clothing & Lifestyle', 'name_ar' => 'اللباس وأسلوب الحياة', 'name_ur' => 'لباس اور طرز زندگی'],
                    ['name' => 'Rights & Etiquettes', 'name_ar' => 'الحقوق والآداب', 'name_ur' => 'حقوق اور آداب'],
                    ['name' => 'Education & Upbringing', 'name_ar' => 'التعليم والتربية', 'name_ur' => 'تعلیم و تربیت'],
                ]
            ],
            [
                'name' => 'Transactions & Dealings',
                'name_ar' => 'معاملات مالية',
                'name_ur' => 'مالیاتی معاملات',
                'sub' => [
                    ['name' => 'Business', 'name_ar' => 'التجارة', 'name_ur' => 'تجارت'],
                    ['name' => 'Shares & Investments', 'name_ar' => 'الأسهم والاستثمار', 'name_ur' => 'حصص اور سرمایہ کاری'],
                    ['name' => 'Interest & Insurance', 'name_ar' => 'الربا والتأمين', 'name_ur' => 'سود اور انشورنس'],
                    ['name' => 'Other Transactions', 'name_ar' => 'معاملات أخرى', 'name_ur' => 'دیگر معاملات'],
                    ['name' => 'Inheritance & Will', 'name_ar' => 'الميراث والوصية', 'name_ur' => 'وراثت اور وصیت'],
                ]
            ],
            [
                'name' => 'Miscellaneous',
                'name_ar' => 'متفرقات',
                'name_ur' => 'متفرقات',
                'sub' => [
                    ['name' => 'Halal & Haram', 'name_ar' => 'الحلال والحرام', 'name_ur' => 'حلال و حرام'],
                    ['name' => 'Dua (Supplications)', 'name_ar' => 'الدعاء', 'name_ur' => 'دعا'],
                    ['name' => 'Islamic Names', 'name_ar' => 'الأسماء الإسلامية', 'name_ur' => 'اسلامی نام'],
                    ['name' => 'Tasawwuf', 'name_ar' => 'التصوف', 'name_ur' => 'تصوف'],
                    ['name' => 'History & Biography', 'name_ar' => 'التاريخ والسيرة', 'name_ur' => 'تاریخ و سیرت'],
                    ['name' => 'Others', 'name_ar' => 'أخرى', 'name_ur' => 'دیگر'],
                ]
            ],
        ];

        foreach ($categories as $cat) {
            $slug = make_slug($cat['name']);
            
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $stmt->execute([$slug]);
            $parentId = $stmt->fetchColumn();

            if (!$parentId) {
                $stmtInsert = $pdo->prepare("INSERT INTO categories (name, name_ar, name_ur, parent_id, slug, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, NULL, ?, 1, 0, NOW(), NOW())");
                $stmtInsert->execute([$cat['name'], $cat['name_ar'], $cat['name_ur'], $slug]);
                $parentId = $pdo->lastInsertId();
            } else {
                $stmtUpdate = $pdo->prepare("UPDATE categories SET name = ?, name_ar = ?, name_ur = ?, parent_id = NULL, updated_at = NOW() WHERE id = ?");
                $stmtUpdate->execute([$cat['name'], $cat['name_ar'], $cat['name_ur'], $parentId]);
            }

            if (isset($cat['sub'])) {
                foreach ($cat['sub'] as $sub) {
                    $subSlug = make_slug($sub['name']);
                    
                    $stmtSub = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
                    $stmtSub->execute([$subSlug]);
                    $subId = $stmtSub->fetchColumn();

                    if (!$subId) {
                        $stmtInsertSub = $pdo->prepare("INSERT INTO categories (name, name_ar, name_ur, parent_id, slug, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, 0, NOW(), NOW())");
                        $stmtInsertSub->execute([$sub['name'], $sub['name_ar'], $sub['name_ur'], $parentId, $subSlug]);
                    } else {
                        $stmtUpdateSub = $pdo->prepare("UPDATE categories SET name = ?, name_ar = ?, name_ur = ?, parent_id = ?, updated_at = NOW() WHERE id = ?");
                        $stmtUpdateSub->execute([$sub['name'], $sub['name_ar'], $sub['name_ur'], $parentId, $subId]);
                    }
                }
            }
        }
        $status[] = ["Fully nested categories and subcategories created & updated successfully.", "success"];

        // E. Seed Site Settings
        $defaultSettings = [
            ['key' => 'site_title', 'value' => 'Darulifta Kauzariyya'],
            ['key' => 'contact_email', 'value' => 'info@daruliftakauzariyya.com'],
            ['key' => 'contact_phone', 'value' => '+91 1234567890'],
            ['key' => 'allow_public_questions', 'value' => '1'],
        ];
        foreach ($defaultSettings as $setting) {
            $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE `value` = ?, updated_at = NOW()");
            $stmt->execute([$setting['key'], $setting['value'], $setting['value']]);
        }
        $status[] = ["Seeded default portal settings successfully.", "success"];

        $status[] = ["🚀 ALL DATABASES MIGRATIONS & SEEDINGS COMPLETED SUCCESSFULLY!", "completed"];

    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

// ---------------- RENDER INTERFACE ----------------
if ($isCli) {
    // Run CLI layout
    echo "\n=== DARULIFTA KAUZARIYYA DATABASE SETUP ===\n\n";
    if ($error) {
        echo "❌ ERROR: " . $error . "\n\n";
        exit(1);
    }
    foreach ($status as $s) {
        $icon = "[*]";
        if ($s[1] === 'success') $icon = "✅";
        if ($s[1] === 'info') $icon = "ℹ️";
        if ($s[1] === 'completed') $icon = "🚀";
        echo $icon . " " . strip_tags($s[0]) . "\n";
    }
    echo "\n============================================\n\n";
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Darulifta Kauzariyya</title>
    <!-- Outfit Font & Bootstrap Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.06);
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #10b981;
            --info: #0ea5e9;
            --text-color: #f3f4f6;
            --text-muted: #9ca3af;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 10% 20%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(16, 185, 129, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 650px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--success));
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 3rem;
            color: var(--success);
            margin-bottom: 15px;
            display: inline-block;
            filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.3));
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            background: linear-gradient(to right, #ffffff, #d1d5db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .config-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .config-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .config-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .config-item {
            font-size: 0.9rem;
        }

        .config-label {
            color: var(--text-muted);
            margin-bottom: 3px;
            font-size: 0.8rem;
        }

        .config-value {
            font-weight: 500;
            font-family: monospace;
            background: rgba(255, 255, 255, 0.04);
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 16px 24px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            gap: 10px;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            cursor: pointer;
            color: var(--text-color);
            user-select: none;
        }

        .checkbox-container input {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        .results {
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 25px;
        }

        .results-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .log-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 250px;
            overflow-y: auto;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .log-item {
            font-size: 0.85rem;
            line-height: 1.5;
            padding-left: 20px;
            position: relative;
        }

        .log-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 7px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .log-item.success {
            color: #a7f3d0;
        }
        .log-item.success::before {
            background-color: var(--success);
            box-shadow: 0 0 8px var(--success);
        }

        .log-item.info {
            color: #bae6fd;
        }
        .log-item.info::before {
            background-color: var(--info);
            box-shadow: 0 0 8px var(--info);
        }

        .log-item.completed {
            color: #ffffff;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.1);
            padding: 8px 12px 8px 30px;
            border-radius: 8px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .log-item.completed::before {
            left: 12px;
            top: 14px;
            background-color: var(--success);
            box-shadow: 0 0 10px var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 15px;
            color: #fca5a5;
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-danger i {
            color: #ef4444;
            font-size: 1.2rem;
        }

        .security-note {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 16px;
            padding: 15px;
            color: #fde047;
            font-size: 0.85rem;
            text-align: center;
            margin-top: 25px;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <span class="logo-icon"><i class="bi bi-database-fill-gear"></i></span>
            <h1>Darulifta Database Setup</h1>
            <p class="subtitle">Initialize tables and populate default schemas & seeding</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Database Connection Error:</strong><br>
                    <?= htmlspecialchars($error) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Database details loaded from .env automatically -->

        <form method="POST">
            <label class="checkbox-container">
                <input type="checkbox" name="fresh" value="1" checked>
                <span>Fresh Setup (Drop existing tables first if they exist)</span>
            </label>

            <button type="submit" name="action" value="install" class="btn btn-primary">
                <i class="bi bi-play-circle-fill"></i> Start Installer & Seeder
            </button>
        </form>

        <?php if (!empty($status)): ?>
            <div class="results">
                <div class="results-title text-success">
                    <i class="bi bi-activity"></i> Execution Progress
                </div>
                <ul class="log-list">
                    <?php foreach ($status as $log): ?>
                        <li class="log-item <?= htmlspecialchars($log[1]) ?>">
                            <?= $log[0] ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="security-note">
            <i class="bi bi-exclamation-octagon-fill me-1"></i>
            <strong>Security Reminder:</strong> Please delete this setup file <code>public/setup_database.php</code> from your server after successful installation.
        </div>
    </div>
</div>

</body>
</html>
