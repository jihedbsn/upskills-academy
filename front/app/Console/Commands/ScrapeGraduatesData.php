<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ScrapeGraduatesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'graduates:scrape {--force : Force re-scrape even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape all graduates data from old-api.upskillsacademy.come API and store in JSON';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting graduates data scraping...');

        try {
            // Fetch main graduates data
            $this->info('Fetching graduates summary data...');
            $response = Http::timeout(30)->get('https://old-api.upskillsacademy.com/api/graduates');

            if (!$response->successful()) {
                $this->error('Failed to fetch graduates data: ' . $response->status());
                return 1;
            }

            $mainData = $response->json();
            $yearlyData = $mainData['yearlyData'] ?? [];

            $this->info('Found ' . count($yearlyData) . ' years of data');

            $completeData = [
                'scraped_at' => now()->toIso8601String(),
                'summary' => $mainData,
                'years' => [],
                'certificates' => []
            ];

            // Fetch detailed data for each year
            $progressBar = $this->output->createProgressBar(count($yearlyData));
            $progressBar->start();

            foreach ($yearlyData as $yearInfo) {
                $year = $yearInfo['year'];

                try {
                    $yearResponse = Http::timeout(30)->get("https://old-api.upskillsacademy.com/api/graduates/{$year}");

                    if ($yearResponse->successful()) {
                        $yearData = $yearResponse->json();
                        $completeData['years'][$year] = $yearData;

                        // Store individual certificates for quick lookup
                        if (isset($yearData['certificates'])) {
                            foreach ($yearData['certificates'] as $cert) {
                                if (isset($cert['serialnumber'])) {
                                    $completeData['certificates'][$cert['serialnumber']] = $cert;
                                }
                            }
                        }

                        $this->newLine();
                        $this->info("✓ Scraped year {$year} - " . count($yearData['certificates'] ?? []) . " certificates");
                    } else {
                        $this->newLine();
                        $this->warn("✗ Failed to fetch data for year {$year}");
                    }

                    // Small delay to avoid overwhelming the server
                    usleep(500000); // 0.5 seconds

                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Error fetching year {$year}: " . $e->getMessage());
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Save to storage
            $jsonData = json_encode($completeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            Storage::disk('local')->put('graduates/complete_data.json', $jsonData);

            // Create a summary file
            $summary = [
                'scraped_at' => $completeData['scraped_at'],
                'total_years' => count($completeData['years']),
                'total_certificates' => count($completeData['certificates']),
                'years_available' => array_keys($completeData['years']),
                'file_size' => strlen($jsonData),
            ];

            Storage::disk('local')->put('graduates/summary.json', json_encode($summary, JSON_PRETTY_PRINT));

            $this->info('✓ Data successfully scraped and saved!');
            $this->info("Total years: {$summary['total_years']}");
            $this->info("Total certificates: {$summary['total_certificates']}");
            $this->info("File size: " . round($summary['file_size'] / 1024 / 1024, 2) . " MB");
            $this->info("Location: storage/app/graduates/complete_data.json");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error during scraping: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
