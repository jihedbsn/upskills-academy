<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AboutAcceuil;

class AboutAcceuilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutAcceuil::create([
            'description_en' => '<p>Welcome to Swedish Academy, your premier destination for quality education and professional development. We are committed to providing exceptional learning experiences that empower students to achieve their academic and career goals.</p>
            
            <p>Our academy combines innovative teaching methods with a comprehensive curriculum designed to meet the evolving needs of today\'s learners. With experienced instructors and state-of-the-art facilities, we ensure that every student receives the support and guidance they need to succeed.</p>
            
            <p>Whether you\'re looking to enhance your skills, pursue a new career path, or simply expand your knowledge, Swedish Academy offers a wide range of programs tailored to your needs. Join our community of successful graduates and take the first step towards a brighter future.</p>',
            
            'description_ar' => '<p>مرحباً بكم في الأكاديمية السويدية، وجهتكم المفضلة للتعليم عالي الجودة والتطوير المهني. نحن ملتزمون بتقديم تجارب تعليمية استثنائية تمكن الطلاب من تحقيق أهدافهم الأكاديمية والمهنية.</p>
            
            <p>تجمع أكاديميتنا بين طرق التدريس المبتكرة ومنهج شامل مصمم لتلبية الاحتياجات المتطورة للمتعلمين اليوم. مع وجود مدربين ذوي خبرة ومرافق حديثة، نضمن أن كل طالب يحصل على الدعم والتوجيه الذي يحتاجه للنجاح.</p>
            
            <p>سواء كنت تبحث عن تحسين مهاراتك، أو متابعة مسار مهني جديد، أو ببساطة توسيع معرفتك، تقدم الأكاديمية السويدية مجموعة واسعة من البرامج المصممة خصيصاً لاحتياجاتك. انضم إلى مجتمع خريجينا الناجحين واتخذ الخطوة الأولى نحو مستقبل أكثر إشراقاً.</p>',
            
            'is_active' => true
        ]);
    }
}
