<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PointsService;
use App\Models\Order;

class CalculateCustomerPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'points:calculate
                            {--student= : Calculate points for a specific student ID only}
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and award points for historical orders that don\'t have points yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentId = $this->option('student');
        $dryRun = $this->option('dry-run');

        $this->info('Customer Points Calculator');
        $this->info('===========================');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Build query for orders without points
        $query = Order::where('payment_success', true)
            ->whereNotNull('student_id')
            ->where('price', '>', 0);

        if ($studentId) {
            $query->where('student_id', $studentId);
            $this->info("Processing orders for student ID: {$studentId}");
        }

        // Find orders that don't have points transactions
        $orders = $query->whereNotExists(function ($subquery) {
            $subquery->select(\DB::raw(1))
                ->from('points_transactions')
                ->whereColumn('points_transactions.order_id', 'orders.id')
                ->where('points_transactions.type', 'earn');
        })->get();

        $this->info("Found {$orders->count()} orders without points");

        if ($orders->count() === 0) {
            $this->info('No orders to process.');
            return 0;
        }

        // Show summary before processing
        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Orders', $orders->count()],
                ['Total Amount', '$' . number_format($orders->sum('price'), 2)],
                ['Estimated Points', number_format($orders->sum(fn($o) => floor($o->price)))],
                ['Unique Students', $orders->pluck('student_id')->unique()->count()],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Orders that would be processed:');

            $displayOrders = $orders->take(20)->map(function ($order) {
                return [
                    $order->id,
                    $order->student_id,
                    '$' . number_format($order->price, 2),
                    floor($order->price) . ' pts',
                    $order->created_at->format('Y-m-d'),
                ];
            });

            $this->table(['Order ID', 'Student ID', 'Amount', 'Points', 'Date'], $displayOrders);

            if ($orders->count() > 20) {
                $this->info("... and " . ($orders->count() - 20) . " more orders");
            }

            return 0;
        }

        // Confirm if not running with --no-interaction
        if (!$this->confirm('Do you want to proceed with awarding points?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Process orders
        $this->newLine();
        $progressBar = $this->output->createProgressBar($orders->count());
        $progressBar->start();

        $pointsService = new PointsService();
        $stats = [
            'orders_processed' => 0,
            'total_points' => 0,
            'students' => [],
        ];

        foreach ($orders as $order) {
            $pointsEarned = $pointsService->awardPointsForPurchase(
                $order->student_id,
                (float) $order->price,
                $order->id,
                "Points for historical order #{$order->id}"
            );

            if ($pointsEarned > 0) {
                $stats['orders_processed']++;
                $stats['total_points'] += $pointsEarned;
                $stats['students'][$order->student_id] =
                    ($stats['students'][$order->student_id] ?? 0) + $pointsEarned;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show results
        $this->info('Processing Complete!');
        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Orders Processed', $stats['orders_processed']],
                ['Total Points Awarded', number_format($stats['total_points'])],
                ['Students Updated', count($stats['students'])],
            ]
        );

        // Show top students by points
        if (count($stats['students']) > 0) {
            $this->newLine();
            $this->info('Top 10 Students by Points Awarded:');

            $topStudents = collect($stats['students'])
                ->sortDesc()
                ->take(10)
                ->map(function ($points, $studentId) {
                    return [
                        $studentId,
                        number_format($points) . ' pts',
                    ];
                })->values()->toArray();

            $this->table(['Student ID', 'Points Awarded'], $topStudents);
        }

        return 0;
    }
}
