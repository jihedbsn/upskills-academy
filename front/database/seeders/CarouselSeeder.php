<?php

namespace Database\Seeders;

use App\Models\Carousel;
use Illuminate\Database\Seeder;

class CarouselSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carousels = [
            [
                'slug_ar' => 'تعلم من أفضل المدربين',
                'slug_en' => 'Learn from the Best Instructors',
                'description_ar' => 'اكتشف دوراتنا الشاملة وابدأ رحلة التعلم اليوم مع أفضل المدربين في المجال الرياضي',
                'description_en' => 'Discover our comprehensive courses and start your learning journey today with the best sports instructors',
                'image' => 'hero-1.jpg',
                'is_active' => true,
                'order' => 1
            ],
            [
                'slug_ar' => 'التدريب الرياضي المتقدم',
                'slug_en' => 'Advanced Sports Training',
                'description_ar' => 'برامج تدريبية متقدمة لتحسين الأداء الرياضي واللياقة البدنية',
                'description_en' => 'Advanced training programs to improve sports performance and physical fitness',
                'image' => 'hero-2.jpg',
                'is_active' => true,
                'order' => 2
            ],
            [
                'slug_ar' => 'الشهادات المعتمدة دولياً',
                'slug_en' => 'Internationally Certified Credentials',
                'description_ar' => 'احصل على شهادات معتمدة دولياً في التدريب الرياضي والتغذية',
                'description_en' => 'Get internationally certified credentials in sports training and nutrition',
                'image' => 'hero-3.jpg',
                'is_active' => true,
                'order' => 3
            ]
        ];

        foreach ($carousels as $carousel) {
            Carousel::create($carousel);
        }
    }
}