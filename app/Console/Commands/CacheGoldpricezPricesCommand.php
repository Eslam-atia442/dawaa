<?php

namespace App\Console\Commands;

use App\Services\GoldpricezApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheGoldpricezPricesCommand extends Command
{
    protected $signature   = 'goldpricez:cache-prices {currency?} {unit?} {--all : Cache all default currencies and units}';
    protected $description = 'Cache gold and silver prices from Goldpricez API forever';

    public function handle(GoldpricezApiService $goldpricezService){
        $this->info('Fetching and caching gold and silver prices from Goldpricez API...');
        $this->newLine();

        // Default currencies and units to cache
        $currencies = ['USD', 'EUR', 'GBP', 'CAD', 'SAR', 'AED', 'EGP', 'KWD'];
        $units      = ['gram', 'ounce'];
        $metals     = ['gold', 'silver'];

        // If specific currency/unit provided via command
        $currencyArg = $this->argument('currency') ?? 'egp';
        $unitArg     = $this->argument('unit') ?? 'gram';


        if ($currencyArg && $unitArg) {
            $currencies = [strtoupper($currencyArg)];
            $units      = [strtolower($unitArg)];
        }

        $successCount = 0;
        $totalCount   = 0;

        // Cache gold prices
        foreach ($currencies as $currency) {
            foreach ($units as $unit) {
                $totalCount++;

                // Cache gold
                $this->info("Caching gold: {$currency}/{$unit}...");
                try {
                    $goldResult = $goldpricezService->getGoldRates($currency, $unit);
                    if ($goldResult) {
                        $currentPrice  = $goldpricezService->getCurrentPrice($goldResult, $currency, $unit);
                        $currencyUpper = strtoupper($currency);
                        $unitLower     = strtolower($unit);
                        $cacheKey      = "goldpricez_gold_{$currencyUpper}_{$unitLower}";

                        $cacheData = [
                            'currency_code'             => $currencyUpper,
                            'unit_type'                 => $unitLower,
                            'metal_type'                => 'gold',
                            'current_price'             => $currentPrice,
                            'gold_update_timestamp'     => $goldpricezService->getGoldUpdateTimestamp($goldResult, $currency),
                            'currency_rate'             => $goldpricezService->getCurrencyRate($goldResult, $currency),
                            'currency_update_timestamp' => $goldpricezService->getCurrencyUpdateTimestamp($goldResult, $currency),
                            'karat_rates'               => $currentPrice !== null ? $goldpricezService->calculateKaratRates($currentPrice) : null,
                            'raw_data'                  => $goldResult,
                            'cached_at'                 => now()->toISOString(),
                        ];

                        Cache::forever($cacheKey, $cacheData);
                        $this->info("   ✓ Gold {$currencyUpper}/{$unitLower} cached successfully!");
                        $successCount++;
                    } else {
                        $this->error("   ✗ Failed to fetch gold {$currency}/{$unit}");
                    }
                } catch (\Exception $e) {
                    $this->error("   ✗ Error caching gold {$currency}/{$unit}: " . $e->getMessage());
                }

                // Cache silver
                $this->info("Caching silver: {$currency}/{$unit}...");
                try {
                    $silverResult = $goldpricezService->getSilverRates($currency, $unit);
                    if ($silverResult) {
                        $currentPrice  = $goldpricezService->getCurrentPrice($silverResult, $currency, $unit);
                        $currencyUpper = strtoupper($currency);
                        $unitLower     = strtolower($unit);
                        $cacheKey      = "goldpricez_silver_{$currencyUpper}_{$unitLower}";

                        $cacheData = [
                            'currency_code' => $currencyUpper,
                            'unit_type'     => $unitLower,
                            'metal_type'    => 'silver',
                            'current_price' => $currentPrice,
                            'raw_data'      => $silverResult,
                            'cached_at'     => now()->toISOString(),
                        ];

                        Cache::forever($cacheKey, $cacheData);
                        $this->info("   ✓ Silver {$currencyUpper}/{$unitLower} cached successfully!");
                        $successCount++;
                    } else {
                        $this->warn("   ⚠ Silver {$currency}/{$unit} not available (API may not support silver)");
                    }
                } catch (\Exception $e) {
                    $this->warn("   ⚠ Error caching silver {$currency}/{$unit}: " . $e->getMessage());
                }

                $this->newLine();
            }
        }

        $this->newLine();
        if ($successCount > 0) {
            $this->info("🎉 {$successCount} prices cached successfully!");
            $this->info('Data cached forever (will be refreshed every 2 hours)');
            return 0;
        }

        $this->error("❌ Failed to cache any prices");
        return 1;
    }
}

