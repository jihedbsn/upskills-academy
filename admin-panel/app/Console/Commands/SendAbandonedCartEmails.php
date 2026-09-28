<?php

namespace App\Console\Commands;

use App\Jobs\SendAbandonedCartReminders;
use Illuminate\Console\Command;

class SendAbandonedCartEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send abandoned cart reminder emails based on time intervals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for abandoned carts that need reminders...');

        SendAbandonedCartReminders::dispatch();

        $this->info('Abandoned cart reminder emails dispatched to queue.');

        return Command::SUCCESS;
    }
}
