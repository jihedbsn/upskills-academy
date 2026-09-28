<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnsureAbandonedCartCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:ensure-coupon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure a 10% COMEBACK coupon exists for abandoned cart third reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for abandoned cart recovery coupon...');

        // Check for existing COMEBACK coupons with 10% discount
        $existingCoupon = DB::table('coupons')
            ->where('code', 'LIKE', 'COMEBACK%')
            ->where('type', 'percentage')
            ->where('valeur', 10)
            ->where('is_active', 1)
            ->where('date_fin', '>=', now())
            ->first();

        if ($existingCoupon) {
            $this->info("✓ Active 10% COMEBACK coupon already exists: {$existingCoupon->code}");
            $this->info("  Valid until: {$existingCoupon->date_fin}");
            return 0;
        }

        // Create new coupon
        $code = 'COMEBACK10';
        $this->warn('No active 10% COMEBACK coupon found. Creating one...');

        try {
            $couponId = DB::table('coupons')->insertGetId([
                'code' => $code,
                'nom' => 'Abandoned Cart Recovery 10%',
                'description' => 'Special 10% discount for returning customers who abandoned their cart',
                'valeur' => 10.00,
                'type' => 'percentage',
                'date_debut' => now()->format('Y-m-d'),
                'date_fin' => now()->addYear()->format('Y-m-d'),
                'usage_limit' => null,
                'usage_count' => 0,
                'is_active' => 1,
                'is_stackable' => 0,
                'stack_priority' => 0,
                'customer_type' => 'all',
                'auto_apply' => 0,
                'montant_minimum' => 0,
                'max_discount_amount' => null,
                'is_public' => 1,
                'first_purchase_only' => 0,
                'cumulative_enabled' => 0,
                'allow_multiple_uses' => 1,
                'max_uses_per_user' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->info("✓ Successfully created coupon: {$code} (ID: {$couponId})");
            $this->info("  Valid from: " . now()->format('Y-m-d') . " to " . now()->addYear()->format('Y-m-d'));

            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to create coupon: {$e->getMessage()}");
            return 1;
        }
    }
}
