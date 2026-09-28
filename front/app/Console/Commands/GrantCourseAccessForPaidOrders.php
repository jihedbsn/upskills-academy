<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\ProductStudent;
use Illuminate\Console\Command;

class GrantCourseAccessForPaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:grant-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant course access for all paid orders that are missing ProductStudent records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for paid orders without course access...');

        // Get all successful course orders
        $courseOrders = Order::where('payment_success', true)
            ->whereNotNull('product_id')
            ->with('product')
            ->get();

        $this->info("Found {$courseOrders->count()} paid course orders");

        $grantedCount = 0;
        $alreadyExistsCount = 0;
        $errorCount = 0;

        foreach ($courseOrders as $order) {
            try {
                // Check if access already exists
                $existing = ProductStudent::where('student_id', $order->student_id)
                    ->where('product_id', $order->product_id)
                    ->first();

                if ($existing) {
                    // Make sure it's active
                    if (!$existing->is_active) {
                        $existing->update([
                            'is_active' => true,
                            'access_granted_at' => now(),
                            'is_expired' => false,
                        ]);
                        $this->info("Reactivated access for Student {$order->student_id} - Course {$order->product_id}");
                        $grantedCount++;
                    } else {
                        $alreadyExistsCount++;
                    }
                } else {
                    // Create new access
                    ProductStudent::create([
                        'student_id' => $order->student_id,
                        'product_id' => $order->product_id,
                        'date' => $order->created_at,
                        'is_active' => true,
                        'access_granted_at' => now(),
                        'is_expired' => false,
                    ]);

                    $this->info("✓ Granted access for Student {$order->student_id} - Course {$order->product_id}");
                    $grantedCount++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Error processing Order {$order->id}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("- Total orders processed: {$courseOrders->count()}");
        $this->info("- Access granted/reactivated: {$grantedCount}");
        $this->info("- Already had access: {$alreadyExistsCount}");
        if ($errorCount > 0) {
            $this->error("- Errors: {$errorCount}");
        }

        $this->newLine();
        $this->info('Done!');

        return 0;
    }
}
