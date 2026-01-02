<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CacheExchangeratesCurrenciesCommand extends Command
{
    protected $signature = 'exchangerates:cache-currencies';

    protected $description = 'Cache latest currency exchange rates in EGP from exchangerates API forever';

    public function handle()
    {
        try {
            $currencies = config('services.exchangerates.currencies', ['USD', 'EUR', 'GBP', 'AED', 'SAR']);
            $token      = config('services.exchangerates.token');
            $baseUrl    = 'https://api.apilayer.com/exchangerates_data';

            if (!$token) {
                $this->error('Exchange rates API token is not configured');
                return self::FAILURE;
            }

            $currencies = array_values(array_unique(array_map(fn ($c) => strtoupper(trim($c)), $currencies)));

            $this->info('Caching latest currency exchange rates: ' . implode(', ', $currencies));
            $this->newLine();

            $successCount = 0;
            $total        = count($currencies);

            foreach ($currencies as $idx => $currency) {
                $this->info(($idx + 1) . ". Caching {$currency} rate...");

                try {
                    $response = Http::withHeaders([
                        'apikey' => $token,
                    ])->get("{$baseUrl}/latest", [
                        'base'    => $currency,
                        'symbols' => 'EGP',
                    ]);

                    if (!$response->successful()) {
                        $this->error("   ✗ Failed to fetch {$currency} rate: " . $response->status());
                        Log::error("Exchange rates API failed for {$currency}", [
                            'status'   => $response->status(),
                            'response' => $response->body(),
                        ]);
                        continue;
                    }

                    $data = $response->json();

                    if (!isset($data['rates']['EGP'])) {
                        $this->error("   ✗ EGP rate not found in response for {$currency}");
                        Log::error("Invalid response structure for {$currency}", [
                            'response' => $data,
                        ]);
                        continue;
                    }

                    $rate = (float)$data['rates']['EGP'];

                    // Cache forever
                    $cacheKey = 'exchangerates_' . strtolower($currency) . '_egp';
                    $cacheData = [
                        'data'      => [
                            'currency' => $currency,
                            'rate'     => $rate,
                            'base'     => $currency,
                            'target'   => 'EGP',
                        ],
                        'cached_at' => now()->toISOString(),
                    ];



                    Cache::forever($cacheKey, $cacheData);

                    $this->info("   ✓ {$currency} rate cached successfully! (1 {$currency} = {$rate} EGP)");
                    $successCount++;
                } catch (\Exception $e) {
                    $this->error("   ✗ Error caching {$currency} rate: " . $e->getMessage());
                    Log::error("Error caching exchange rate", [
                        'currency' => $currency,
                        'error'    => $e->getMessage(),
                        'trace'    => $e->getTraceAsString(),
                    ]);
                }
                $this->newLine();
            }

            if ($successCount === $total) {
                $this->info("🎉 All {$total} currencies cached successfully!");
                $this->info('Data cached forever (until you run this command again)');
                return self::SUCCESS;
            }

            if ($successCount > 0) {
                $this->warn("⚠️  {$successCount}/{$total} currencies cached successfully");
                return self::SUCCESS;
            }

            $this->error("❌ All {$total} currencies failed to cache");
            return self::FAILURE;
        } catch (\Throwable $t) {
            $this->error('Failed to cache currency exchange rates: ' . $t->getMessage());
            Log::error('Failed to cache currency exchange rates', [
                'error' => $t->getMessage(),
                'trace' => $t->getTraceAsString(),
            ]);
            return self::FAILURE;
        }
    }
}

