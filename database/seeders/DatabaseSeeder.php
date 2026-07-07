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

        $this->command->info('Roles, madhabs, default users, and categories seeded successfully.');
        $this->command->info('Admin  → admin@daruliftaportal.com / Admin@123');
        $this->command->info('Mufti  → mufti@daruliftaportal.com / Mufti@123');
    }
}
