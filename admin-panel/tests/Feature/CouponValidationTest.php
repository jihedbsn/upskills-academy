<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CouponValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that store validation works correctly
     */
    public function test_store_coupon_validation_fails_with_missing_required_fields()
    {
        $response = $this->post(route('coupons.store'), []);

        $response->assertSessionHasErrors([
            'nom',
            'valeur',
            'date_debut',
            'date_fin',
            'type',
            'products'
        ]);
    }

    /**
     * Test that store validation works correctly with valid data
     */
    public function test_store_coupon_validation_passes_with_valid_data()
    {
        // Create a product for testing
        $product = Product::factory()->create(['statut' => 1]);

        $validData = [
            'nom' => 'Test Coupon',
            'valeur' => 10.50,
            'date_debut' => now()->format('Y-m-d'),
            'date_fin' => now()->addDays(30)->format('Y-m-d'),
            'type' => 'percentage',
            'limit_utilise' => 100,
            'products' => [$product->id]
        ];

        $response = $this->post(route('coupons.store'), $validData);

        $response->assertRedirect(route('coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'nom' => 'Test Coupon',
            'valeur' => 10.50,
            'type' => 'percentage'
        ]);
    }

    /**
     * Test update validation with invalid date range
     */
    public function test_update_coupon_validation_fails_with_invalid_date_range()
    {
        $coupon = Coupon::factory()->create();

        $invalidData = [
            'nom' => 'Updated Coupon',
            'valeur' => 15.00,
            'date_debut' => now()->addDays(10)->format('Y-m-d'),
            'date_fin' => now()->format('Y-m-d'), // End date before start date
            'type' => 'fixed',
            'products' => [1]
        ];

        $response = $this->put(route('coupons.update', $coupon), $invalidData);

        $response->assertSessionHasErrors(['date_fin']);
    }

    /**
     * Test that custom validation messages are in French
     */
    public function test_validation_messages_are_in_french()
    {
        $response = $this->post(route('coupons.store'), [
            'nom' => '', // Missing required field
            'valeur' => -5, // Invalid value
            'type' => 'invalid_type' // Invalid type
        ]);

        $response->assertSessionHasErrors();
        
        // Check that the error messages contain French text
        $errors = session('errors');
        $this->assertStringContainsString('requis', $errors->first('nom'));
        $this->assertStringContainsString('positive', $errors->first('valeur'));
    }
}