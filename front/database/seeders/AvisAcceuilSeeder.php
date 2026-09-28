<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AvisAcceuil;

class AvisAcceuilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AvisAcceuil::create([
            'client' => 'Marie Dubois',
            'image' => 'client1.jpg',
            'avis_en' => 'Swedish Academy has transformed my career completely. The courses are comprehensive, the instructors are experts in their fields, and the learning environment is exceptional. I highly recommend this academy to anyone looking to advance their skills.',
            'avis_ar' => 'لقد غيرت الأكاديمية السويدية مسيرتي المهنية بالكامل. الدورات شاملة، والمدربون خبراء في مجالاتهم، وبيئة التعلم استثنائية. أنصح بشدة بهذه الأكاديمية لأي شخص يبحث عن تطوير مهاراته.',
            'is_active' => true,
            'order' => 1
        ]);

        AvisAcceuil::create([
            'client' => 'Thomas Martin',
            'image' => 'client2.jpg',
            'avis_en' => 'The quality of education at Swedish Academy is outstanding. The practical approach to learning and the support from instructors made my learning journey smooth and effective. I am now confident in my professional abilities.',
            'avis_ar' => 'جودة التعليم في الأكاديمية السويدية متميزة. النهج العملي في التعلم والدعم من المدربين جعل رحلة تعلمي سلسة وفعالة. أنا الآن واثق من قدراتي المهنية.',
            'is_active' => true,
            'order' => 2
        ]);

        AvisAcceuil::create([
            'client' => 'Sophie Laurent',
            'image' => 'client3.jpg',
            'avis_en' => 'Swedish Academy provided me with the knowledge and skills I needed to excel in my field. The flexible learning options and expert guidance helped me achieve my career goals faster than I expected.',
            'avis_ar' => 'قدمت لي الأكاديمية السويدية المعرفة والمهارات التي احتجتها للتميز في مجالي. خيارات التعلم المرنة والتوجيه الخبير ساعداني في تحقيق أهدافي المهنية أسرع مما توقعت.',
            'is_active' => true,
            'order' => 3
        ]);
    }
}
