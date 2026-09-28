<?php

namespace App\Console\Commands;

use App\Services\InstallmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * ProcessOverdueInstallments Command
 * 
 * Daily cron job that runs at 12:00 AM to:
 * 1. Find all pending installments that are past their due date
 * 2. Mark them as "overdue"
 * 3. Suspend the student's account (revoke dashboard access)
 * 4. Apply the daily $5 late fee to each overdue installment
 * 
 * Penalty Protocol:
 * - Day 0 (Due Date): If no payment webhook is received, account status → Suspended
 * - Student immediately loses access to the Dashboard
 * - A message is displayed: "Please pay your overdue installment to restore access."
 * - Daily late fee of $5 accumulates until payment is completed
 */
class ProcessOverdueInstallments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'installments:process-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Process overdue installments: mark as overdue, suspend accounts, apply daily late fees';

    protected InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        parent::__construct();
        $this->installmentService = $installmentService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Processing overdue installments...');

        // Step 1: Process overdue installments and suspend accounts
        $overdueStats = $this->installmentService->processOverdueInstallments();

        $this->info("Overdue Processing Results:");
        $this->info("  - Checked: {$overdueStats['total_checked']}");
        $this->info("  - Newly Overdue: {$overdueStats['newly_overdue']}");
        $this->info("  - Newly Suspended: {$overdueStats['newly_suspended']}");
        $this->info("  - Errors: {$overdueStats['errors']}");

        // Step 2: Apply daily late fees
        $lateFeeStats = $this->installmentService->applyDailyLateFees();

        $this->info("\nLate Fee Results:");
        $this->info("  - Total Overdue: {$lateFeeStats['total_overdue']}");
        $this->info("  - Fees Applied: {$lateFeeStats['fees_applied']}");
        $this->info("  - Total Fee Amount: \${$lateFeeStats['total_fee_amount']}");
        $this->info("  - Errors: {$lateFeeStats['errors']}");

        // Log summary
        Log::info('ProcessOverdueInstallments completed', [
            'overdue_stats' => $overdueStats,
            'late_fee_stats' => $lateFeeStats,
        ]);

        $this->info("\nDone!");

        return Command::SUCCESS;
    }
}
