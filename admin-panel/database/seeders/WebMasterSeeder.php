<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WebMaster;
use Illuminate\Support\Facades\Hash;

class WebMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Web Master principal
        WebMaster::create([
            'first_name' => 'Web',
            'last_name' => 'Master',
            'email' => 'webmaster@swedishacademy.com',
            'password' => Hash::make('password123'),
            'phone' => '+46 123 456 792',
            'role' => 'web_master',
            'permissions' => [
                'manage_content',
                'manage_products',
                'manage_students',
                'view_analytics',
                'manage_discussions',
                'manage_ratings',
            ],
            'is_active' => true,
        ]);

        // Content Manager
        WebMaster::create([
            'first_name' => 'Content',
            'last_name' => 'Manager',
            'email' => 'content@swedishacademy.com',
            'password' => Hash::make('password123'),
            'phone' => '+46 123 456 793',
            'role' => 'content_manager',
            'permissions' => [
                'manage_content',
                'manage_products',
                'view_analytics',
                'manage_discussions',
                'manage_ratings',
            ],
            'is_active' => true,
        ]);

        // Support
        WebMaster::create([
            'first_name' => 'Support',
            'last_name' => 'Team',
            'email' => 'support@swedishacademy.com',
            'password' => Hash::make('password123'),
            'phone' => '+46 123 456 794',
            'role' => 'support',
            'permissions' => [
                'view_analytics',
                'manage_discussions',
                'manage_ratings',
            ],
            'is_active' => true,
        ]);

        $this->command->info('WebMasters créés avec succès !');
        $this->command->info('Web Master: webmaster@swedishacademy.com / password123');
        $this->command->info('Content Manager: content@swedishacademy.com / password123');
        $this->command->info('Support: support@swedishacademy.com / password123');
    }
}
