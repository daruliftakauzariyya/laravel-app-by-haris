<?php

/**
 * Standalone Category Seeder for Fatwa Portal
 * 
 * Instructions:
 * 1. Upload this file to your public/ directory on the live server.
 * 2. Visit: https://daruliftakauzariyya.com/seed_categories_standalone.php
 * 3. DELETE THIS FILE after running for security!
 */

// Database Configuration (Matching your live environment)
$dbConfig = [
    'host' => '162.214.80.164', 
    'dbname' => 'ensplpmy_darulifta-lar-db',
    'user' => 'ensplpmy_darulifta-lar-use',
    'pass' => 'DaurIfta123@2026',
];

// Simple Slug Generator (Replacement for Laravel's Str::slug)
function create_slug($text) {
    if (empty($text)) return '';
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text;
}

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4",
        $dbConfig['user'],
        $dbConfig['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<h3>Connected to Database successfully!</h3>";
} catch (PDOException $e) {
    die("<h3 style='color:red;'>Database Connection failed: " . $e->getMessage() . "</h3>");
}

// Category Data
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

echo "<h4>Starting Category Seeding...</h4>";

foreach ($categories as $cat) {
    $slug = create_slug($cat['name']);
    
    // Insert or Update Parent Category
    try {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $parentId = $stmt->fetchColumn();

        if ($parentId) {
            $stmtUpdate = $pdo->prepare("UPDATE categories SET name = ?, name_ar = ?, name_ur = ?, parent_id = NULL, updated_at = NOW() WHERE id = ?");
            $stmtUpdate->execute([$cat['name'], $cat['name_ar'], $cat['name_ur'], $parentId]);
            echo "<p style='color:blue'>&mdash; Updated Parent Category: <strong>{$cat['name']}</strong></p>";
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO categories (name, name_ar, name_ur, parent_id, slug, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, NULL, ?, 1, 0, NOW(), NOW())");
            $stmtInsert->execute([$cat['name'], $cat['name_ar'], $cat['name_ur'], $slug]);
            $parentId = $pdo->lastInsertId();
            echo "<p style='color:green'>&mdash; Created Parent Category: <strong>{$cat['name']}</strong></p>";
        }

        // Process Subcategories
        if (isset($cat['sub'])) {
            foreach ($cat['sub'] as $sub) {
                $subSlug = create_slug($sub['name']);
                
                $stmtSub = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
                $stmtSub->execute([$subSlug]);
                $subId = $stmtSub->fetchColumn();

                if ($subId) {
                    $stmtUpdateSub = $pdo->prepare("UPDATE categories SET name = ?, name_ar = ?, name_ur = ?, parent_id = ?, updated_at = NOW() WHERE id = ?");
                    $stmtUpdateSub->execute([$sub['name'], $sub['name_ar'], $sub['name_ur'], $parentId, $subId]);
                    echo "<p style='color:blue; margin-left:20px;'>&bull; Updated Subcategory: {$sub['name']}</p>";
                } else {
                    $stmtInsertSub = $pdo->prepare("INSERT INTO categories (name, name_ar, name_ur, parent_id, slug, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, 0, NOW(), NOW())");
                    $stmtInsertSub->execute([$sub['name'], $sub['name_ar'], $sub['name_ur'], $parentId, $subSlug]);
                    echo "<p style='color:green; margin-left:20px;'>&bull; Created Subcategory: {$sub['name']}</p>";
                }
            }
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error processing category {$cat['name']}: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Seeding Complete!</h3>";
echo "<p style='color: darkred;'><strong>SECURITY REMINDER:</strong> Please delete this file <code>seed_categories_standalone.php</code> from your server now.</p>";

?>
