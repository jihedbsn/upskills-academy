<?php

namespace App\Console\Commands;

use App\Jobs\SendAbandonedCartReminders;
use App\Services\AbandonedCartService;
use Illuminate\Console\Command;

class TrackAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:track-abandoned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track abandoned carts and send reminder emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Tracking abandoned carts...');

        $service = new AbandonedCartService();
        $service->trackAbandonedCarts();

        $this->info('Abandoned carts tracked successfully.');

        $this->info('Sending reminder emails...');

        SendAbandonedCartReminders::dispatch();

        $this->info('Reminder job dispatched successfully.');

        return Command::SUCCESS;
    }
}
