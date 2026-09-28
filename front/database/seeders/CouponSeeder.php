<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'nom' => 'Welcome Discount',
                'description' => '10% off for new customers',
                'type' => 'percentage',
                'valeur' => 10.00,
                'montant_minimum' => 50.00,
                'usage_limit' => 100,
                'date_debut' => now(),
                'date_fin' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'SAVE20',
                'nom' => 'Save 20%',
                'description' => '20% off on orders over $100',
                'type' => 'percentage',
                'valeur' => 20.00,
                'montant_minimum' => 100.00,
                'usage_limit' => 50,
                'date_debut' => now(),
                'date_fin' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'FLAT15',
                'nom' => 'Flat $15 Off',
                'description' => '$15 off on any order',
                'type' => 'fixed',
                'valeur' => 15.00,
                'montant_minimum' => 75.00,
                'usage_limit' => 200,
                'date_debut' => now(),
                'date_fin' => now()->addMonths(1),
                'is_active' => true,
            ],
            [
                'code' => 'STUDENT25',
                'nom' => 'Student Special',
                'description' => '25% off for students',
                'type' => 'percentage',
                'valeur' => 25.00,
                'montant_minimum' => 30.00,
                'usage_limit' => null,
                'date_debut' => now(),
                'date_fin' => now()->addYear(),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
