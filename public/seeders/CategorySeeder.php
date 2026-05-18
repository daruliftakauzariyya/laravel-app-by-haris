<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'name_ar' => $cat['name_ar'] ?? null,
                    'name_ur' => $cat['name_ur'] ?? null,
                    'parent_id' => null,
                    'is_active' => true,
                ]
            );

            foreach ($cat['sub'] as $sub) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($sub['name'])],
                    [
                        'name' => $sub['name'],
                        'name_ar' => $sub['name_ar'] ?? null,
                        'name_ur' => $sub['name_ur'] ?? null,
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
