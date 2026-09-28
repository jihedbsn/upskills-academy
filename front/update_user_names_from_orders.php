<?php

/**
 * Script to update user first_name and last_name from recent orders
 * This script finds users with missing names and updates them from their order data
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Starting user name update from orders...\n\n";

try {
    // Get recent 10 orders where the student has empty first_name or last_name
    $orders = DB::table('orders')
        ->join('students', 'orders.student_id', '=', 'students.id')
        ->whereNotNull('orders.first_name')
        ->whereNotNull('orders.last_name')
        ->where(function($query) {
            $query->whereNull('students.first_name')
                  ->orWhereNull('students.last_name')
                  ->orWhere('students.first_name', '')
                  ->orWhere('students.last_name', '');
        })
        ->select(
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
        ->limit(10)
        ->get();

    if ($orders->isEmpty()) {
        echo "✓ No users found with missing names.\n";
        exit(0);
    }

    echo "Found " . count($orders) . " orders with students needing name updates:\n\n";

    $updatedCount = 0;
    $processedStudents = [];

    foreach ($orders as $order) {
        // Skip if we already processed this student
        if (in_array($order->student_id, $processedStudents)) {
            continue;
        }

        $processedStudents[] = $order->student_id;

        echo "Processing Student ID: {$order->student_id}\n";
        echo "  Email: {$order->email}\n";
        echo "  Current Name: '{$order->student_first_name}' '{$order->student_last_name}'\n";
        echo "  Order Name: '{$order->order_first_name}' '{$order->order_last_name}'\n";

        // Update the student record
        $updated = DB::table('students')
            ->where('id', $order->student_id)
            ->update([
                'first_name' => $order->order_first_name,
                'last_name' => $order->order_last_name,
                'updated_at' => now(),
            ]);

        if ($updated) {
            echo "  ✓ Updated successfully!\n\n";
            $updatedCount++;
        } else {
            echo "  ✗ Update failed!\n\n";
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo "  Total orders checked: " . count($orders) . "\n";
    echo "  Students updated: {$updatedCount}\n";
    echo str_repeat("=", 50) . "\n";

    echo "\n✓ Script completed successfully!\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
