<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partnership;

class PartnershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partnerships = [
            [
                'institution_name' => 'Tunisian Sports Academy',
                'institution_address' => '123 Avenue Habib Bourguiba, Tunis, Tunisia',
                'email' => 'contact@tunisiansportsacademy.tn',
                'phone' => '+216 71 123 456',
                'website' => 'https://tunisiansportsacademy.tn',
                'profile_file' => 'tunisian_sports_academy_profile.pdf',
                'requested_courses' => ['sports_injuries', 'sports_nutrition', 'fitness_trainer'],
                'additional_courses' => 'We are also interested in advanced coaching techniques for team sports.',
                'status' => 'pending',
                'notes' => 'Potential partner with strong local network',
                'submitted_at' => now()->subDays(5),
            ],
            [
                'institution_name' => 'Moroccan Fitness Institute',
                'institution_address' => '456 Boulevard Mohammed V, Casablanca, Morocco',
                'email' => 'info@moroccanfitness.ma',
                'phone' => '+212 522 987 654',
                'website' => 'https://moroccanfitness.ma',
                'profile_file' => 'moroccan_fitness_institute_profile.pdf',
                'requested_courses' => ['personal_trainer', 'sports_psychology', 'sports_management'],
                'additional_courses' => null,
                'status' => 'approved',
                'notes' => 'Approved partnership - excellent credentials',
                'submitted_at' => now()->subDays(10),
            ],
            [
                'institution_name' => 'Algerian Sports Center',
                'institution_address' => '789 Rue Didouche Mourad, Algiers, Algeria',
                'email' => 'partnership@algeriansports.dz',
                'phone' => '+213 21 456 789',
                'website' => null,
                'profile_file' => 'algerian_sports_center_profile.pdf',
                'requested_courses' => ['sports_rehabilitation', 'fitness_assistant'],
                'additional_courses' => 'Looking for specialized courses in youth sports development.',
                'status' => 'rejected',
                'notes' => 'Incomplete documentation provided',
                'submitted_at' => now()->subDays(15),
            ],
            [
                'institution_name' => 'Egyptian Athletic Club',
                'institution_address' => '321 Tahrir Square, Cairo, Egypt',
                'email' => 'admin@egyptianathletic.eg',
                'phone' => '+20 2 1234 5678',
                'website' => 'https://egyptianathletic.eg',
                'profile_file' => 'egyptian_athletic_club_profile.pdf',
                'requested_courses' => ['sports_injuries', 'sports_nutrition', 'personal_trainer', 'sports_psychology'],
                'additional_courses' => null,
                'status' => 'pending',
                'notes' => 'Large institution with multiple locations',
                'submitted_at' => now()->subDays(3),
            ],
            [
                'institution_name' => 'Libyan Sports Academy',
                'institution_address' => '654 Tripoli Street, Tripoli, Libya',
                'email' => 'contact@libyansports.ly',
                'phone' => '+218 21 987 654',
                'website' => 'https://libyansports.ly',
                'profile_file' => 'libyan_sports_academy_profile.pdf',
                'requested_courses' => ['fitness_trainer', 'sports_management'],
                'additional_courses' => 'Interested in courses related to sports facility management.',
                'status' => 'pending',
                'notes' => 'New partnership request - under review',
                'submitted_at' => now()->subDay(),
            ],
        ];

        foreach ($partnerships as $partnership) {
            Partnership::create($partnership);
        }
    }
}
