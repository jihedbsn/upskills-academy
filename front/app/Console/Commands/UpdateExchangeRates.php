<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:refresh-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh exchange rates cache from free open-source APIs (Frankfurter, ExchangeRate-API)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Refreshing exchange rates from API...');

        $currencyService = new CurrencyService();
        $rates = $currencyService->refreshRates();

        if (!empty($rates)) {
            $this->info("✓ Exchange rates refreshed successfully!");
            $this->info("  Rates cached for 30 minutes");
            $this->table(
                ['Currency', 'Rate (USD)'],
                collect($rates)->map(fn($rate, $code) => [$code, $rate])->take(15)->toArray()
            );
        } else {
            $this->error("✗ Failed to refresh exchange rates");
            return 1;
        }

        return 0;
    }
}
