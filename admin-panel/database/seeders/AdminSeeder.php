<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        Admin::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@swedishacademy.com',
            'password' => Hash::make('password123'),
            'phone' => '+46 123 456 789',
            'role' => 'super_admin',
            'permissions' => [
                'manage_users',
                'manage_products',
                'manage_orders',
                'manage_students',
                'manage_certificates',
                'view_analytics',
                'manage_settings',
                'manage_content',
                'manage_discussions',
                'manage_ratings',
            ],
            'is_active' => true,
        ]);

        // Admin normal
        Admin::create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@swedishacademy.com',
            'password' => Hash::make('password123'),
            'phone' => '+46 123 456 790',
            'role' => 'admin',
            'permissions' => [
                'manage_products',
                'manage_orders',
                'manage_students',
                'manage_certificates',
                'view_analytics',
                'manage_content',
                'manage_discussions',
                'manage_ratings',
            ],
            'is_active' => true,
        ]);

        // Modérateur
        Admin::create([
            'first_name' => 'Moderateur',
            'last_name' => 'Content',
            'email' => 'moderator@swedishacademy.com',
            'password' => Hash::make('password123'),
            'phone' => '+46 123 456 791',
            'role' => 'moderator',
            'permissions' => [
                'manage_content',
                'manage_discussions',
                'manage_ratings',
                'view_analytics',
            ],
            'is_active' => true,
        ]);

        $this->command->info('Admins créés avec succès !');
        $this->command->info('Super Admin: superadmin@swedishacademy.com / password123');
        $this->command->info('Admin: admin@swedishacademy.com / password123');
        $this->command->info('Moderateur: moderator@swedishacademy.com / password123');
    }
}
