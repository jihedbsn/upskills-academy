<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PartnershipCourse;

class PartnershipCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partnershipCourses = [
            [
                'institution_name' => 'Sports Training Center Tunisia',
                'institution_address' => '123 Avenue Habib Bourguiba, Tunis, Tunisia',
                'phone' => '+216 20 123 456',
                'email' => 'info@sportstraining.tn',
                'course_title_en' => 'Advanced Sports Nutrition Certification',
                'course_type' => 'training_course',
                'delivery_method' => 'classroom',
                'evaluation_method' => 'theoretical_practical_test',
                'course_description' => 'A comprehensive course covering advanced sports nutrition principles, meal planning for athletes, and practical application in sports performance.',
                'website_url' => 'https://sportstraining.tn',
                'status' => 'pending',
                'submitted_at' => now(),
            ],
            [
                'institution_name' => 'Fitness Academy Morocco',
                'institution_address' => '456 Boulevard Mohammed V, Casablanca, Morocco',
                'phone' => '+212 5 22 123 456',
                'email' => 'contact@fitnessacademy.ma',
                'course_title_en' => 'Personal Training Workshop',
                'course_type' => 'workshop',
                'delivery_method' => 'online',
                'evaluation_method' => 'practical_test',
                'course_description' => 'Intensive workshop focusing on personal training techniques, client assessment, and program design for fitness professionals.',
                'website_url' => 'https://fitnessacademy.ma',
                'status' => 'approved',
                'submitted_at' => now()->subDays(5),
            ],
        ];

        foreach ($partnershipCourses as $course) {
            PartnershipCourse::create($course);
        }
    }
}
