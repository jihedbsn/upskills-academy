<?php

namespace App\Console\Commands;

use App\Services\InstallmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessOverdueInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installments:process-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process overdue installments, suspend accounts, and apply daily late fees.';

    public function __construct(private readonly InstallmentService $installmentService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Processing overdue installments...');

        $overdueStats = $this->installmentService->processOverdueInstallments();
        $lateFeeStats = $this->installmentService->applyDailyLateFees();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Checked installments', (string) $overdueStats['total_checked']],
                ['Newly overdue', (string) $overdueStats['newly_overdue']],
                ['Newly suspended', (string) $overdueStats['newly_suspended']],
                ['Overdue processing errors', (string) $overdueStats['errors']],
                ['Total overdue installments', (string) $lateFeeStats['total_overdue']],
                ['Late fees applied', (string) $lateFeeStats['fees_applied']],
                ['Late fee amount applied', '$' . number_format((float) $lateFeeStats['total_fee_amount'], 2)],
                ['Late fee errors', (string) $lateFeeStats['errors']],
            ]
        );

        Log::info('installments:process-overdue completed', [
            'overdue' => $overdueStats,
            'late_fees' => $lateFeeStats,
        ]);

        return self::SUCCESS;
    }
}
