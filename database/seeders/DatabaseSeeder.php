<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $adminRole      = Role::firstOrCreate(['name' => 'admin']);
        $muftiRole      = Role::firstOrCreate(['name' => 'mufti']);
        $questionerRole = Role::firstOrCreate(['name' => 'questioner']);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@daruliftaportal.com'],
            [
                'name'              => 'Admin',
                'password'          => bcrypt('Admin@123'),
                'profile_completed' => true,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Create a demo Mufti
        $mufti = User::firstOrCreate(
            ['email' => 'mufti@daruliftaportal.com'],
            [
                'name'              => 'Mufti Demo',
                'password'          => bcrypt('Mufti@123'),
                'profile_completed' => true,
                'is_active'         => true,
                'email_verified_at' => now(),
                'gender'            => 'male',
                'country'           => 'India',
                'mother_language'   => 'Urdu',
            ]
        );
        $mufti->assignRole($muftiRole);

        // Seed Categories
        $this->call(CategorySeeder::class);

        $this->command->info('Roles, admin user, and categories seeded successfully.');
        $this->command->info('Admin  → admin@daruliftaportal.com / Admin@123');
        $this->command->info('Mufti  → mufti@daruliftaportal.com / Mufti@123');
    }
}
