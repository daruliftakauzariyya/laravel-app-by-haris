<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks during structural changes
        Schema::disableForeignKeyConstraints();

        // 1. Drop obsolete and non-compliant tables
        Schema::dropIfExists('questions');
        Schema::dropIfExists('mufti_answers');
        Schema::dropIfExists('supporting_references');
        Schema::dropIfExists('fatwas');
        Schema::dropIfExists('fatwa_verifier_user');
        Schema::dropIfExists('fatwa_likes');
        Schema::dropIfExists('fatwa_user_views');
        Schema::dropIfExists('fatwa_view_snapshots');
        Schema::dropIfExists('fatwa_featured_rules');
        Schema::dropIfExists('portal_visitor_logs');
        Schema::dropIfExists('question_files');
        Schema::dropIfExists('madhab_user');
        
        // Drop Spatie Pivot/Permission Tables
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        
        // Cache & Queue Tables
        Schema::dropIfExists('cache');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

        // 2. Roles Table
        Schema::dropIfExists('roles');
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        // Seed Roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin', 'guard_name' => 'web', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 2, 'name' => 'mufti', 'guard_name' => 'web', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 3, 'name' => 'questioner', 'guard_name' => 'web', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
        ]);

        // 3. Madhabs Table
        Schema::dropIfExists('madhabs');
        Schema::create('madhabs', function (Blueprint $table) {
            $table->increments('id'); // increments instead of static integer primary
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        // Seed Madhabs
        DB::table('madhabs')->insert([
            ['id' => 1, 'name' => 'Hanafi', 'slug' => 'hanafi', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 2, 'name' => 'Shafii', 'slug' => 'shafii', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 5, 'name' => 'General/Other', 'slug' => 'general-other', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
        ]);

        // 4. Site Settings Table
        Schema::dropIfExists('site_settings');
        Schema::create('site_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key');
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // Seed Site Settings
        DB::table('site_settings')->insert([
            ['id' => 1, 'key' => 'site_title', 'value' => 'Darul Ifta Kauzariyya', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 2, 'key' => 'contact_email', 'value' => 'darulifta.kauzariyya@gmail.com', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 3, 'key' => 'contact_phone', 'value' => '+91 9747650176', 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
        ]);

        // 5. Categories Table
        Schema::dropIfExists('categories');
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('name_ur')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedInteger('stars')->default(5);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });

        // Seed Categories
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Faiths & Beliefs', 'name_ar' => 'عقائد وإيمانيات', 'name_ur' => 'عقائد و ایمانیات', 'parent_id' => NULL, 'slug' => 'faiths-beliefs', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 2, 'name' => 'Islamic Beliefs', 'name_ar' => 'العقائد الإسلامية', 'name_ur' => 'اسلامی عقائد', 'parent_id' => 1, 'slug' => 'islamic-beliefs', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 3, 'name' => 'World Religions', 'name_ar' => 'الأديان العالمية', 'name_ur' => 'عالمی مذاہب', 'parent_id' => 1, 'slug' => 'world-religions', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 4, 'name' => 'False Sects', 'name_ar' => 'الفرق الباطلة', 'name_ur' => 'باطل فرقے', 'parent_id' => 1, 'slug' => 'false-sects', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 5, 'name' => 'Deviant Sects', 'name_ar' => 'الفرق المنحرفة', 'name_ur' => 'منحرف فرقے', 'parent_id' => 1, 'slug' => 'deviant-sects', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 6, 'name' => 'Innovations & Customs', 'name_ar' => 'البدع والرسوم', 'name_ur' => 'بدعات و رسومات', 'parent_id' => 1, 'slug' => 'innovations-customs', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 7, 'name' => 'Taqleed & Fiqhi Schools', 'name_ar' => 'التقليد والمذاهب الفقهية', 'name_ur' => 'تقلید و فقہی مذاہب', 'parent_id' => 1, 'slug' => 'taqleed-fiqhi-schools', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 8, 'name' => 'The Holy Quran', 'name_ar' => 'القرآن الكريم', 'name_ur' => 'قرآن کریم', 'parent_id' => 1, 'slug' => 'the-holy-quran', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 9, 'name' => 'Hadith & Sunnah', 'name_ar' => 'الحديث والسنة', 'name_ur' => 'حدیث و سنت', 'parent_id' => 1, 'slug' => 'hadith-sunnah', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 10, 'name' => 'Dawah & Tableeg', 'name_ar' => 'الدعوة والتبليغ', 'name_ur' => 'دعوت و تبلیغ', 'parent_id' => 1, 'slug' => 'dawah-tableeg', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 11, 'name' => 'Prayers & Duties', 'name_ar' => 'عبادات وفرائض', 'name_ur' => 'عبادات و فرائض', 'parent_id' => NULL, 'slug' => 'prayers-duties', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 12, 'name' => 'Taharah (Purity)', 'name_ar' => 'الطهارة', 'name_ur' => 'طہارت', 'parent_id' => 11, 'slug' => 'taharah-purity', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 13, 'name' => 'Salah (Prayer)', 'name_ar' => 'الصلاة', 'name_ur' => 'نماز', 'parent_id' => 11, 'slug' => 'salah-prayer', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 14, 'name' => 'Jumuah & Eid Prayers', 'name_ar' => 'صلاة الجمعة والعيدين', 'name_ur' => 'جمعہ اور عیدین', 'parent_id' => 11, 'slug' => 'jumuah-eid-prayers', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 15, 'name' => 'Death & Funeral', 'name_ar' => 'الموت والجنائز', 'name_ur' => 'موت و جنائز', 'parent_id' => 11, 'slug' => 'death-funeral', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 16, 'name' => 'Sawm (Fasting)', 'name_ar' => 'الصوم', 'name_ur' => 'روزہ', 'parent_id' => 11, 'slug' => 'sawm-fasting', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 17, 'name' => 'Zakat & Charity', 'name_ar' => 'الزكاة والصدقات', 'name_ur' => 'زکوۃ و صدقات', 'parent_id' => 11, 'slug' => 'zakat-charity', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 18, 'name' => 'Hajj & Umrah', 'name_ar' => 'الحج والعمرة', 'name_ur' => 'حج و عمرہ', 'parent_id' => 11, 'slug' => 'hajj-umrah', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 19, 'name' => 'Oaths & Vows', 'name_ar' => 'الأيمان والنذور', 'name_ur' => 'قسمیں اور نذریں', 'parent_id' => 11, 'slug' => 'oaths-vows', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 20, 'name' => 'Waqf, Mosque, Madrasa', 'name_ar' => 'الوقف والمسجد والمدرسة', 'name_ur' => 'وقف، مسجد اور مدرسہ', 'parent_id' => 11, 'slug' => 'waqf-mosque-madrasa', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 21, 'name' => 'Qurbani (Slaughtering)', 'name_ar' => 'الأضحية والذبح', 'name_ur' => 'قربانی اور ذبح', 'parent_id' => 11, 'slug' => 'qurbani-slaughtering', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 22, 'name' => 'Social Matters', 'name_ar' => 'معاملات اجتماعية', 'name_ur' => 'سماجی معاملات', 'parent_id' => NULL, 'slug' => 'social-matters', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 23, 'name' => 'Nikah (Marriage)', 'name_ar' => 'النكاح', 'name_ur' => 'نکاح', 'parent_id' => 22, 'slug' => 'nikah-marriage', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 24, 'name' => 'Talaq (Divorce)', 'name_ar' => 'الطلاق', 'name_ur' => 'طلاق', 'parent_id' => 22, 'slug' => 'talaq-divorce', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 25, 'name' => 'Food & Drinks', 'name_ar' => 'الأطعمة والأشربة', 'name_ur' => 'کھانے پینے کی چیزیں', 'parent_id' => 22, 'slug' => 'food-drinks', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 26, 'name' => 'Clothing & Lifestyle', 'name_ar' => 'اللباس وأسلوب الحياة', 'name_ur' => 'لباس اور طرز زندگی', 'parent_id' => 22, 'slug' => 'clothing-lifestyle', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 27, 'name' => 'Rights & Etiquettes', 'name_ar' => 'الحقوق والآداب', 'name_ur' => 'حقوق اور آداب', 'parent_id' => 22, 'slug' => 'rights-etiquettes', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 28, 'name' => 'Education & Upbringing', 'name_ar' => 'التعليم والتربية', 'name_ur' => 'تعلیم و تربیت', 'parent_id' => 22, 'slug' => 'education-upbringing', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 29, 'name' => 'Transactions & Dealings', 'name_ar' => 'معاملات مالية', 'name_ur' => 'مالیاتی معاملات', 'parent_id' => NULL, 'slug' => 'transactions-dealings', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 30, 'name' => 'Business', 'name_ar' => 'التجارة', 'name_ur' => 'تجارت', 'parent_id' => 29, 'slug' => 'business', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 31, 'name' => 'Shares & Investments', 'name_ar' => 'الأسهم والاستثمار', 'name_ur' => 'حصص اور سرمایہ کاری', 'parent_id' => 29, 'slug' => 'shares-investments', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 32, 'name' => 'Interest & Insurance', 'name_ar' => 'الربا والتأمين', 'name_ur' => 'سود اور انشورنس', 'parent_id' => 29, 'slug' => 'interest-insurance', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 33, 'name' => 'Other Transactions', 'name_ar' => 'معاملات أخرى', 'name_ur' => 'دیگر معاملات', 'parent_id' => 29, 'slug' => 'other-transactions', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 34, 'name' => 'Inheritance & Will', 'name_ar' => 'الميراث والوصية', 'name_ur' => 'وراثت اور وصیت', 'parent_id' => 29, 'slug' => 'inheritance-will', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 35, 'name' => 'Miscellaneous', 'name_ar' => 'متفرقات', 'name_ur' => 'متفرقات', 'parent_id' => NULL, 'slug' => 'miscellaneous', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 36, 'name' => 'Halal & Haram', 'name_ar' => 'الحلال والحرام', 'name_ur' => 'حلال و حرام', 'parent_id' => 35, 'slug' => 'halal-haram', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 37, 'name' => 'Dua (Supplications)', 'name_ar' => 'الدعاء', 'name_ur' => 'دعا', 'parent_id' => 35, 'slug' => 'dua-supplications', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 38, 'name' => 'Islamic Names', 'name_ar' => 'الأسماء الإسلامية', 'name_ur' => 'اسلامی نام', 'parent_id' => 35, 'slug' => 'islamic-names', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 39, 'name' => 'Tasawwuf', 'name_ar' => 'التصوف', 'name_ur' => 'تصوف', 'parent_id' => 35, 'slug' => 'tasawwuf', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 40, 'name' => 'History & Biography', 'name_ar' => 'التاريخ والسيرة', 'name_ur' => 'تاریخ و سیرت', 'parent_id' => 35, 'slug' => 'history-biography', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 41, 'name' => 'Others', 'name_ar' => 'أخرى', 'name_ur' => 'دیگر', 'parent_id' => 35, 'slug' => 'others', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-05-18 09:49:38', 'updated_at' => '2026-05-18 09:49:38'],
            ['id' => 42, 'name' => 'Women\'s Rulings', 'name_ar' => 'أحكام النساء', 'name_ur' => 'خواتین کے احکام / جدید مسائل', 'parent_id' => NULL, 'slug' => 'womens-rulings-6a4240d47d073', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-06-29 21:24:28', 'updated_at' => '2026-06-29 21:24:28'],
            ['id' => 43, 'name' => 'Women\'s Travel & Driving', 'name_ar' => 'سفر المرأة وقضايا القيادة', 'name_ur' => 'خواتین کا سفر اور ڈرائیونگ', 'parent_id' => 42, 'slug' => 'womens-travel-driving-6a42415bcd34a', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-06-29 21:26:43', 'updated_at' => '2026-06-29 21:26:43'],
            ['id' => 49, 'name' => 'Halal & Haram (Permissibility) / Sports & Entertainment', 'name_ar' => 'الحلال والحرام / الألعاب والترفيه', 'name_ur' => 'حلال و حرام / کھیل اور تفریح', 'parent_id' => NULL, 'slug' => 'halal-haram-permissibility-sports-entertainment-6a43ba0f3ed65', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-07-01 00:13:59', 'updated_at' => '2026-07-01 00:13:59'],
            ['id' => 50, 'name' => 'Mobile Gaming & Leisure', 'name_ar' => 'ألعاب الهاتف والملاهي', 'name_ur' => 'موبائل گیمنگ اور لہو و لعب', 'parent_id' => 49, 'slug' => 'mobile-gaming-leisure-6a43ba4447ffe', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-07-01 00:14:52', 'updated_at' => '2026-07-01 00:14:52'],
            ['id' => 51, 'name' => 'Halal & Haram (Permissibility) / Financial Transactions', 'name_ar' => 'الحلال والحرام / المعاملات المالية', 'name_ur' => 'حلال و حرام / مالیاتی معاملات', 'parent_id' => NULL, 'slug' => 'halal-haram-permissibility-financial-transactions-6a43bab1af82b', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-07-01 00:16:41', 'updated_at' => '2026-07-01 00:16:41'],
            ['id' => 52, 'name' => 'Banking & Interest (Riba)', 'name_ar' => 'البنوك والربا', 'name_ur' => 'بینکنگ اور سود', 'parent_id' => 51, 'slug' => 'banking-interest-riba-6a43badc45dfb', 'is_active' => 1, 'sort_order' => 0, 'created_at' => '2026-07-01 00:17:24', 'updated_at' => '2026-07-01 00:17:24'],
        ]);

        // 6. Users Table
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('name');
            $table->string('despl_name'); // Display name
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable();
            $table->string('avatar')->nullable();
            $table->string('gender')->nullable();
            $table->string('country')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country_code')->nullable();
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('mother_language')->nullable();
            $table->unsignedSmallInteger('mufti_sort_order')->default(0);
            $table->unsignedInteger('madhab_id')->nullable();
            $table->tinyInteger('profile_completed')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('can_check_approve')->default(0);
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('madhab_id')->references('id')->on('madhabs')->onDelete('set null');
        });

        // 7. Fatwa Activity Logs Table
        Schema::dropIfExists('fatwa_activity_logs');
        Schema::create('fatwa_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('fatwa_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event');
            $table->string('event_label');
            $table->text('notes')->nullable();
            $table->string('icon')->default('bi-circle');
            $table->string('color', 50)->default('secondary');
            $table->timestamp('event_at')->nullable();
            $table->timestamps();
        });

        // 8. Question And Answer Table
        Schema::dropIfExists('question_and_answer');
        Schema::create('question_and_answer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('question_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedInteger('madhab_id')->nullable();
            $table->unsignedBigInteger('assigned_mufti_id')->nullable();
            $table->string('title')->nullable();
            $table->longText('body');
            $table->string('language')->default('English');
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('status');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->longText('answer')->nullable();
            $table->longText('draft_answer')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('verified_by')->nullable();
            $table->unsignedBigInteger('checked_approved_by')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('stars')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_mufti_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('published_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checked_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('madhab_id')->references('id')->on('madhabs')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        // 9. References Table
        Schema::dropIfExists('references');
        Schema::create('references', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('question_and_answer_id');
            $table->longText('arabic_text');
            $table->string('book_name');
            $table->string('language')->default('Arabic');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_draft')->default(false);
            $table->timestamps();

            $table->foreign('question_and_answer_id')
                  ->references('id')
                  ->on('question_and_answer')
                  ->onDelete('cascade');
        });

        // 10. Question Status History Table
        Schema::dropIfExists('question_status_history');
        Schema::create('question_status_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('question_and_answer_id');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->unsignedBigInteger('previous_assigned_mufti_id')->nullable();
            $table->unsignedBigInteger('new_assigned_mufti_id')->nullable();
            $table->unsignedBigInteger('user_id'); // actor
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('question_and_answer_id')
                  ->references('id')
                  ->on('question_and_answer')
                  ->onDelete('cascade');
            $table->foreign('previous_assigned_mufti_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('new_assigned_mufti_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('question_status_history');
        Schema::dropIfExists('references');
        Schema::dropIfExists('question_and_answer');
        Schema::dropIfExists('fatwa_activity_logs');
        Schema::dropIfExists('users');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('madhabs');
        Schema::enableForeignKeyConstraints();
    }
};
