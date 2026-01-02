<?php

namespace App\Console\Commands;

use App\Services\EgratesApiService;
use Illuminate\Console\Command;

class UpdateCurrenciesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency prices and bank prices for all configured currencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting currency prices update...');

        // Check if EGRATES_TOKEN is configured
        $token = config('services.egrates.token');
        if (!$token) {
            $this->error('EGRATES_TOKEN is not configured. Please set the EGRATES_TOKEN environment variable.');
            return 1;
        }

        $egratesService = new EgratesApiService();
        $currencies = config('services.exchangerates.currencies', []);

        if (empty($currencies)) {
            $this->error('No currencies configured in services.exchangerates.currencies');
            return 1;
        }

        $this->info("Found " . count($currencies) . " currencies to update: " . implode(', ', $currencies));

        foreach ($currencies as $currency) {
            $this->info("Updating currency: {$currency}");

            // Call getCurrencyPrices method
            $this->info("Fetching currency prices for {$currency}...");
            $currencyPrices = $egratesService->getCurrencyPrices($currency, true);

            if ($currencyPrices) {
                $this->info("✓ Currency prices for {$currency} updated successfully");
            } else {
                $this->error("✗ Failed to update currency prices for {$currency}");
            }

            // Call banksPrices method
            $this->info("Fetching bank prices for {$currency}...");
            $bankPrices = $egratesService->banksPrices($currency, true);

            if ($bankPrices) {
                $this->info("✓ Bank prices for {$currency} updated successfully");
            } else {
                $this->error("✗ Failed to update bank prices for {$currency}");
            }
        }

        $this->info('Currency prices update completed.');
        return 0;
    }
}
