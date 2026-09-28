<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserNamesFromOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-names-from-orders {--student= : Specific student ID or email} {--limit=10 : Number of recent orders to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user first_name and last_name from their recent orders if missing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $studentFilter = $this->option('student');

        $this->info("Starting user name update from orders...\n");

        try {
            // Build the query
            $query = DB::table('orders')
                ->join('students', 'orders.student_id', '=', 'students.id');

            // If specific student is provided, filter by ID or email
            if ($studentFilter) {
                if (is_numeric($studentFilter)) {
                    $query->where('students.id', $studentFilter);
                    $this->info("Filtering for student ID: {$studentFilter}\n");
                } else {
                    $query->where('students.email', $studentFilter);
                    $this->info("Filtering for student email: {$studentFilter}\n");
                }

                // For specific student, get all orders even if first_name/last_name are null
                $orders = $query->select(
                        'students.id as student_id',
                        'students.first_name as student_first_name',
                        'students.last_name as student_last_name',
                        'students.email',
                        'orders.id as order_id',
                        'orders.first_name as order_first_name',
                        'orders.last_name as order_last_name',
                        'orders.created_at'
                    )
                    ->orderBy('orders.created_at', 'desc')
                    ->limit($limit)
                    ->get();

                // Show debug info
                if ($orders->isEmpty()) {
                    $this->warn("No orders found for this student.");

                    // Check if student exists
                    $studentExists = DB::table('students')
                        ->where(is_numeric($studentFilter) ? 'id' : 'email', $studentFilter)
                        ->exists();

                    if (!$studentExists) {
                        $this->error("Student not found in database!");
                    } else {
                        $this->info("Student exists but has no orders.");
                    }
                    return 0;
                }

                $this->info("Found " . count($orders) . " order(s) for this student:\n");
            } else {
                // Only check for missing names if no specific student is provided
                $query->whereNotNull('orders.first_name')
                      ->whereNotNull('orders.last_name')
                      ->where(function($q) {
                          $q->whereNull('students.first_name')
                            ->orWhereNull('students.last_name')
                            ->orWhere('students.first_name', '')
                            ->orWhere('students.last_name', '');
                      });

                $orders = $query->select(
                        'students.id as student_id',
                        'students.first_name as student_first_name',
                        'students.last_name as student_last_name',
                        'students.email',
                        'orders.id as order_id',
                        'orders.first_name as order_first_name',
                        'orders.last_name as order_last_name',
                        'orders.created_at'
                    )
                    ->orderBy('orders.created_at', 'desc')
                    ->limit($limit)
                    ->get();
            }

            if ($orders->isEmpty() && !$studentFilter) {
                $this->info("✓ No users found with missing names.");
                return 0;
            }

            $this->info("Found " . count($orders) . " orders with students needing name updates:\n");

            $updatedCount = 0;
            $processedStudents = [];

            foreach ($orders as $order) {
                // Skip if we already processed this student
                if (in_array($order->student_id, $processedStudents)) {
                    continue;
                }

                $processedStudents[] = $order->student_id;

                $this->line("Processing Student ID: {$order->student_id}");
                $this->line("  Email: {$order->email}");
                $this->line("  Current Name: '{$order->student_first_name}' '{$order->student_last_name}'");
                $this->line("  Order Name: '{$order->order_first_name}' '{$order->order_last_name}'");

                // Check if update is needed
                if (empty($order->order_first_name) || empty($order->order_last_name)) {
                    $this->warn("  ⚠️  Skipped - Order has no name data\n");
                    continue;
                }

                // Update the student record
                $updated = DB::table('students')
                    ->where('id', $order->student_id)
                    ->update([
                        'first_name' => $order->order_first_name,
                        'last_name' => $order->order_last_name,
                        'updated_at' => now(),
                    ]);

                if ($updated) {
                    $this->info("  ✓ Updated successfully!\n");
                    $updatedCount++;
                } else {
                    $this->error("  ✗ Update failed!\n");
                }
            }

            $this->newLine();
            $this->info(str_repeat("=", 50));
            $this->info("Summary:");
            $this->info("  Total orders checked: " . count($orders));
            $this->info("  Students updated: {$updatedCount}");
            $this->info(str_repeat("=", 50));

            $this->newLine();
            $this->info("✓ Command completed successfully!");

            return 0;

        } catch (\Exception $e) {
            $this->error("\n✗ Error: " . $e->getMessage());
            $this->error("Stack trace:\n" . $e->getTraceAsString());
            return 1;
        }
    }
}
