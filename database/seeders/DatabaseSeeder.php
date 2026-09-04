<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Madhab;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole      = Role::firstOrCreate(['name' => 'admin']);
        $muftiRole      = Role::firstOrCreate(['name' => 'mufti']);
        $questionerRole = Role::firstOrCreate(['name' => 'questioner']);

        // Create Madhabs
        $hanafi  = Madhab::firstOrCreate(['slug' => 'hanafi'], ['name' => 'Hanafi']);
        $shafii  = Madhab::firstOrCreate(['slug' => 'shafii'], ['name' => 'Shafii']);
        $maliki  = Madhab::firstOrCreate(['slug' => 'maliki'], ['name' => 'Maliki']);
        $hanbali = Madhab::firstOrCreate(['slug' => 'hanbali'], ['name' => 'Hanbali']);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@daruliftaportal.com'],
            [
                'name'              => 'Admin',
                'despl_name'        => 'Admin User',
                'password'          => bcrypt('Admin@123'),
                'role_id'           => $adminRole->id,
                'profile_completed' => true,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // Create a demo Mufti
        $mufti = User::firstOrCreate(
            ['email' => 'mufti@daruliftaportal.com'],
            [
                'name'              => 'Mufti Demo',
                'despl_name'        => 'Mufti Demo',
                'password'          => bcrypt('Mufti@123'),
                'role_id'           => $muftiRole->id,
                'madhab_id'         => $hanafi->id,
                'profile_completed' => true,
                'is_active'         => true,
                'email_verified_at' => now(),
                'gender'            => 'male',
                'country'           => 'India',
                'mother_language'   => 'Urdu',
            ]
        );

        // Seed Categories
        $this->call(CategorySeeder::class);

        // Create a demo Questioner
        $questioner = User::firstOrCreate(
            ['email' => 'user@daruliftaportal.com'],
            [
                'name'              => 'Questioner Demo',
                'despl_name'        => 'Questioner Demo',
                'password'          => bcrypt('User@123'),
                'role_id'           => $questionerRole->id,
                'profile_completed' => true,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed Sample Questions and Fatwas
        \App\Models\QuestionAndAnswer::firstOrCreate(
            ['question_id' => 'Q-1001'],
            [
                'user_id' => $questioner->id,
                'category_id' => 12, // Taharah (Purity)
                'madhab_id' => $hanafi->id,
                'title' => 'Doubt regarding Wudu validity',
                'body' => 'I have a doubt regarding whether my Wudu is invalidated if a small drop of blood appears but does not flow.',
                'status' => 'pending',
                'views' => 0,
            ]
        );

        \App\Models\QuestionAndAnswer::firstOrCreate(
            ['question_id' => 'Q-1002'],
            [
                'user_id' => $questioner->id,
                'category_id' => 13, // Salah (Prayer)
                'madhab_id' => $hanafi->id,
                'assigned_mufti_id' => $mufti->id,
                'title' => 'Missing a Rak\'ah in congregation',
                'body' => 'What is the correct procedure for completing the prayer if I joined the congregation in the second Rak\'ah of Zuhr?',
                'status' => 'assigned',
                'views' => 0,
            ]
        );

        \App\Models\QuestionAndAnswer::firstOrCreate(
            ['question_id' => 'Q-1003'],
            [
                'user_id' => $questioner->id,
                'category_id' => 16, // Sawm (Fasting)
                'madhab_id' => $hanafi->id,
                'assigned_mufti_id' => $mufti->id,
                'title' => 'Using inhaler during fast',
                'body' => 'Does using an asthma inhaler invalidate the fast according to the Hanafi Madhab?',
                'answer' => 'Yes, using an asthma inhaler invalidates the fast because the medicine enters the throat. If one is forced to use it, they must keep a Qada fast later.',
                'status' => 'published',
                'published_by' => $admin->id,
                'published_at' => now()->subDays(2),
                'views' => 142,
                'likes' => 12,
                'stars' => 5,
            ]
        );

        \App\Models\QuestionAndAnswer::firstOrCreate(
            ['question_id' => 'Q-1004'],
            [
                'user_id' => $questioner->id,
                'category_id' => 17, // Zakat & Charity
                'madhab_id' => $hanafi->id,
                'assigned_mufti_id' => $mufti->id,
                'title' => 'Zakat on home property',
                'body' => 'Is Zakat due on the house I reside in, or is it only due on investment properties?',
                'answer' => 'Zakat is not due on the house you reside in, as it falls under personal necessities. However, Zakat is due on any properties purchased for commercial/reselling purposes.',
                'status' => 'published',
                'published_by' => $admin->id,
                'published_at' => now()->subDay(),
                'views' => 256,
                'likes' => 18,
                'stars' => 5,
            ]
        );

        $this->command->info('Roles, madhabs, default users, categories, and sample questions seeded successfully.');
        $this->command->info('Admin      → admin@daruliftaportal.com / Admin@123');
        $this->command->info('Mufti      → mufti@daruliftaportal.com / Mufti@123');
        $this->command->info('Questioner → user@daruliftaportal.com / User@123');
    }
}
