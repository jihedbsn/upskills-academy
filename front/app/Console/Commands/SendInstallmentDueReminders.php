<?php

namespace App\Console\Commands;

use App\Services\InstallmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInstallmentDueReminders extends Command
{
    /**
     * @var string
     */
    protected $signature = 'installments:send-due-reminders';

    /**
     * @var string
     */
    protected $description = 'Send installment reminders 20/10/5/1 days before due date.';

    public function __construct(private readonly InstallmentService $installmentService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Sending installment due reminders...');

        $stats = $this->installmentService->sendDueInstallmentReminders();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Checked installments', (string) $stats['checked']],
                ['Reminders sent', (string) $stats['sent']],
                ['Skipped', (string) $stats['skipped']],
                ['Errors', (string) $stats['errors']],
            ]
        );

        Log::info('installments:send-due-reminders completed', $stats);

        return self::SUCCESS;
    }
}
