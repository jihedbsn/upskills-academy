<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sujet;

class SujetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sujets = [
            // Sujets en arabe (Ar -> ar) - FA
            ['description' => 'المفاصل أنواعها تشريحيا ووظيفيا وأهميتها في (التمارين) الرياضية وكيفية تطويرها ووقايتها من الإصابات', 'lang' => 'ar', 'type' => 'fa'],
            ['description' => 'الاربطة أنواعها تشريحا ووظيفيا وأهميتها في التمارين الرياضية وكيفية تطويرها ووقايتها من الإصابات', 'lang' => 'ar', 'type' => 'fa'],
            ['description' => 'مفصل الركبة تشريحا ووظيفيا وأهميته في التمارين الرياضية وكيفية تطويره ووقايتيه من الإصابات', 'lang' => 'ar', 'type' => 'fa'],
            ['description' => 'مفصل الورك والحركات الناجمة عنه ومدى اهميتيه في الحركات والنشاط الرياضي', 'lang' => 'ar', 'type' => 'fa'],
            ['description' => 'الظهر بصورة عامة تشريحيا ووظيفيا واهميتيه في التمارين الرياضية', 'lang' => 'ar', 'type' => 'fa'],
            
            // Sujets en arabe - FI
            ['description' => 'الأطراف العليا تشريحيا ووظيفيا واهميتيها في النشاط الرياضي', 'lang' => 'ar', 'type' => 'fi'],
            ['description' => 'الأطراف السفلى تشريحيا ووظيفيا واهميتيها في النشاط الرياضي', 'lang' => 'ar', 'type' => 'fi'],
            ['description' => 'القوة أنواعها وطرق تطويرها بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'fi'],
            ['description' => 'السرعة أنواعها وطرق تطويرها بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'fi'],
            ['description' => 'البوليمتريك أنواعه وطرق تطويره بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'fi'],
            
            // Sujets en arabe - PT
            ['description' => 'المرونة أنواعها وطرق تطويرها بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'pt'],
            ['description' => 'تمارين الجذع core exercises   أنواعها وطرق تطويرها بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'pt'],
            ['description' => 'نظم انتاج الطاقة أنواعها وطرق تطويرها بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'pt'],
            ['description' => 'العضلات تشريحها وأهميتها وطرق تطويرها بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'pt'],
            ['description' => 'الانقباضات العضلية أنواعها أهميتها وطرق تطويرها بدنيا وفيزيولوجيا', 'lang' => 'ar', 'type' => 'pt'],
            
            // Sujets en arabe - Autres
            ['description' => 'الجهاز العصبي تشريحه ووظائفه وأهميته وطرق تطويره', 'lang' => 'ar', 'type' => 'autres'],
            ['description' => 'القلب تشريحه ووظائفه وأهميته وطرق تطويره حسب متطلبات النشاط الرياضي المتخصص', 'lang' => 'ar', 'type' => 'autres'],
            ['description' => 'التغذية الرياضية والمكملات الغذائية قبل واثناء وبعد النشاط الرياضي', 'lang' => 'ar', 'type' => 'autres'],
            ['description' => 'الحالة النفسية والذهنية وتأثيرها على النشاط الرياضي', 'lang' => 'ar', 'type' => 'autres'],
            ['description' => 'ظواهر الإرهاق العضلي والفرق بين التعب العضلي والألم العضلي وتأثيرها على النشاط البدني', 'lang' => 'ar', 'type' => 'autres'],
            
            // Sujets en français (Fr -> en) - FA
            ['description' => 'Les articulations : Leurs types anatomiques et fonctionnels, leur importance dans l\'exercice, et comment ils se développent et de se protéger contre les blessures', 'lang' => 'en', 'type' => 'fa'],
            ['description' => 'les ligaments : Leurs types anatomiques et fonctionnels, leur importance dans l\'exercice, et comment ils se développent et de se protéger contre les blessures', 'lang' => 'en', 'type' => 'fa'],
            ['description' => 'L\'anatomie et la fonction de l\'articulation du genou, son importance dans l\'exercice, et comment la développer et éviter les blessures les blessures', 'lang' => 'en', 'type' => 'fa'],
            ['description' => 'L\'articulation de la hanche ou (coxo-fémorale) et les mouvements qui en résultent et son importance dans les mouvements et l\'activité sportive', 'lang' => 'en', 'type' => 'fa'],
            ['description' => 'Le dos en général anatomiquement et fonctionnellement et son importance dans les exercices sportifs.', 'lang' => 'en', 'type' => 'fa'],
            
            // Sujets en français - FI
            ['description' => 'Les membres supérieurs : anatomiquement et fonctionnellement et leur importance dans l\'activité sportive', 'lang' => 'en', 'type' => 'fi'],
            ['description' => 'Les membres inférieurs : anatomiquement, fonctionnellement et leur importance dans l\'activité sportive', 'lang' => 'en', 'type' => 'fi'],
            ['description' => 'la Force : ses types et méthodes de la développer physiquement et physiologiquement', 'lang' => 'en', 'type' => 'fi'],
            ['description' => 'la vitesse : ses types et méthodes de la développer physiquement et physiologiquement', 'lang' => 'en', 'type' => 'fi'],
            ['description' => 'La Polymétrie : ses types et ses méthodes de la développer physiquement et physiologiquement', 'lang' => 'en', 'type' => 'fi'],
            
            // Sujets en français - PT
            ['description' => 'la Flexibilité : ses types et ses méthodes des développements physiques et physiologiques', 'lang' => 'en', 'type' => 'pt'],
            ['description' => 'Exercices du Tronc : ses types et méthodes pour les développer à la fois physiquement et physiologiquement', 'lang' => 'en', 'type' => 'pt'],
            ['description' => 'Systèmes de production d\'énergie : types et méthodes pour leur développement physique et physiologique', 'lang' => 'en', 'type' => 'pt'],
            ['description' => '- les muscles : leur anatomie, leur importance et leurs méthodes de développement physique et physiologique', 'lang' => 'en', 'type' => 'pt'],
            ['description' => 'Contractions musculaires : leurs types, leur importance et les méthodes pour les développer physiquement et physiologiquement', 'lang' => 'en', 'type' => 'pt'],
            
            // Sujets en français - Autres
            ['description' => 'Anatomie du système nerveux, fonctions, importance et méthodes de le développer', 'lang' => 'en', 'type' => 'autres'],
            ['description' => 'le Cœur : Anatomie, fonctions, importance et méthodes du développer selon les exigences de l\'activité sportive spécialisée', 'lang' => 'en', 'type' => 'autres'],
            ['description' => 'Nutrition sportive et compléments nutritionnels avant, pendant et après l\'activité sportive', 'lang' => 'en', 'type' => 'autres'],
            ['description' => 'État psychologique et mental et ses effets sur l\'activité sportive', 'lang' => 'en', 'type' => 'autres'],
            ['description' => 'Symptômes de fatigue musculaire et la différence entre la fatigue musculaire et les douleurs musculaires et leur effet sur l\'activité physique.', 'lang' => 'en', 'type' => 'autres'],
        ];

        foreach ($sujets as $sujet) {
            Sujet::create($sujet);
        }
    }
}
