<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductAcceuil;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Teacher;

class ProductAcceuilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des teachers de test
        $teacher1 = Teacher::create([
            'nom' => 'Martin',
            'prenom' => 'Dubois',
            'email' => 'martin.dubois@example.com',
            'password' => bcrypt('password'),
        ]);

        $teacher2 = Teacher::create([
            'nom' => 'Sophie',
            'prenom' => 'Laurent',
            'email' => 'sophie.laurent@example.com',
            'password' => bcrypt('password'),
        ]);

        $teacher3 = Teacher::create([
            'nom' => 'Thomas',
            'prenom' => 'Martin',
            'email' => 'thomas.martin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Créer des produits de test
        $product1 = Product::create([
            'teacher_id' => $teacher1->id,
            'period' => 12,
            'point' => 100,
            'prix' => 89.00,
            'statut' => 'active',
            'online' => true,
            'classroom' => false,
            'images' => 'course1.jpg',
        ]);

        $product2 = Product::create([
            'teacher_id' => $teacher2->id,
            'period' => 15,
            'point' => 120,
            'prix' => 129.00,
            'statut' => 'active',
            'online' => true,
            'classroom' => false,
            'images' => 'course2.jpg',
        ]);

        $product3 = Product::create([
            'teacher_id' => $teacher3->id,
            'period' => 18,
            'point' => 150,
            'prix' => 159.00,
            'statut' => 'active',
            'online' => true,
            'classroom' => false,
            'images' => 'course3.jpg',
        ]);

        // Créer des variations de produits en anglais
        ProductVariation::create([
            'products_id' => $product1->id,
            'name' => 'Sports Coaching Fundamentals',
            'slug' => 'sports-coaching-fundamentals',
            'short_description' => 'Learn the essential principles of sports coaching and athlete development.',
            'langue' => 'en',
        ]);

        ProductVariation::create([
            'products_id' => $product2->id,
            'name' => 'Sports Nutrition Mastery',
            'slug' => 'sports-nutrition-mastery',
            'short_description' => 'Master the science of sports nutrition for optimal performance.',
            'langue' => 'en',
        ]);

        ProductVariation::create([
            'products_id' => $product3->id,
            'name' => 'Sports Psychology & Mental Training',
            'slug' => 'sports-psychology-mental-training',
            'short_description' => 'Develop mental strength and psychological skills for athletes.',
            'langue' => 'en',
        ]);

        // Créer des variations de produits en arabe
        ProductVariation::create([
            'products_id' => $product1->id,
            'name' => 'أساسيات التدريب الرياضي',
            'slug' => 'اساسيات-التدريب-الرياضي',
            'short_description' => 'تعلم المبادئ الأساسية للتدريب الرياضي وتطوير الرياضيين.',
            'langue' => 'ar',
        ]);

        ProductVariation::create([
            'products_id' => $product2->id,
            'name' => 'إتقان التغذية الرياضية',
            'slug' => 'اتقان-التغذية-الرياضية',
            'short_description' => 'أتقن علم التغذية الرياضية للأداء الأمثل.',
            'langue' => 'ar',
        ]);

        ProductVariation::create([
            'products_id' => $product3->id,
            'name' => 'علم النفس الرياضي والتدريب الذهني',
            'slug' => 'علم-النفس-الرياضي-والتدريب-الذهني',
            'short_description' => 'طور القوة الذهنية والمهارات النفسية للرياضيين.',
            'langue' => 'ar',
        ]);

        // Créer des produits d'accueil
        ProductAcceuil::create([
            'product_id' => $product1->id,
            'is_active' => true,
            'order' => 1,
        ]);

        ProductAcceuil::create([
            'product_id' => $product2->id,
            'is_active' => true,
            'order' => 2,
        ]);

        ProductAcceuil::create([
            'product_id' => $product3->id,
            'is_active' => true,
            'order' => 3,
        ]);
    }
}
