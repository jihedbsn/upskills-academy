<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUserNamesInOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-names {--limit=20 : Number of recent orders to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user names vs order names for verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        $this->info("Checking user names against order data...\n");

        try {
            // Get recent orders with student data
            $orders = DB::table('orders')
                ->join('students', 'orders.student_id', '=', 'students.id')
                ->whereNotNull('orders.first_name')
                ->whereNotNull('orders.last_name')
                ->select(
                    'students.id as student_id',
                    'students.email',
                    'students.first_name as user_first_name',
                    'students.last_name as user_last_name',
                    'orders.id as order_id',
                    'orders.first_name as order_first_name',
                    'orders.last_name as order_last_name',
                    'orders.created_at'
                )
                ->orderBy('orders.created_at', 'desc')
                ->limit($limit)
                ->get();

            if ($orders->isEmpty()) {
                $this->warn("No orders found.");
                return 0;
            }

            $this->info("Found {$orders->count()} recent orders:\n");

            $table = [];
            $missingCount = 0;
            $mismatchCount = 0;

            foreach ($orders as $order) {
                $userEmpty = empty($order->user_first_name) || empty($order->user_last_name);
                $mismatch = !$userEmpty && (
                    $order->user_first_name !== $order->order_first_name ||
                    $order->user_last_name !== $order->order_last_name
                );

                $status = $userEmpty ? '❌ MISSING' : ($mismatch ? '⚠️  MISMATCH' : '✓ OK');

                if ($userEmpty) $missingCount++;
                if ($mismatch) $mismatchCount++;

                $table[] = [
                    $order->student_id,
                    substr($order->email, 0, 25),
                    "'{$order->user_first_name}' '{$order->user_last_name}'",
                    "'{$order->order_first_name}' '{$order->order_last_name}'",
                    $status,
                ];
            }

            $this->table(
                ['Student ID', 'Email', 'User Name', 'Order Name', 'Status'],
                $table
            );

            $this->newLine();
            $this->info("Summary:");
            $this->info("  Total orders: {$orders->count()}");
            $this->info("  Users with missing names: {$missingCount}");
            $this->info("  Users with mismatched names: {$mismatchCount}");

            if ($missingCount > 0 || $mismatchCount > 0) {
                $this->newLine();
                $this->warn("Run 'php artisan users:update-names-from-orders' to fix missing names.");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
