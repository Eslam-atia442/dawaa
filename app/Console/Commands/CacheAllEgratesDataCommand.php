<?php

namespace App\Console\Commands;

use App\Services\EgratesApiService;
use Illuminate\Console\Command;

class CacheAllEgratesDataCommand extends Command
{
    protected $signature = 'egrates:cache-all {--force : Force refresh cache even if data exists}';
    protected $description = 'Cache all data from Egrates APIs (USD prices, gold prices, and banks)';

    public function handle(EgratesApiService $egratesService)
    {
        $this->info('Starting to cache all Egrates API data...');
        $this->newLine();

        $successCount = 0;
        $totalApis = 6;

        // Cache USD prices
        $this->info('1. Caching USD prices...');
        try {
            $data = $egratesService->getUsdPrices();
            if ($data) {
                $this->info('   ✓ USD prices cached successfully!');
                $successCount++;
            } else {
                $this->error('   ✗ Failed to cache USD prices');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error caching USD prices: ' . $e->getMessage());
        }
        $this->newLine();

        // Cache EUR prices
        $this->info('2. Caching EUR prices...');
        try {
            $data = $egratesService->getEurPrices();
            if ($data) {
                $this->info('   ✓ EUR prices cached successfully!');
                $successCount++;
            } else {
                $this->error('   ✗ Failed to cache EUR prices');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error caching EUR prices: ' . $e->getMessage());
        }
        $this->newLine();

        // Cache GBP prices
        $this->info('3. Caching GBP prices...');
        try {
            $data = $egratesService->getGbpPrices();
            if ($data) {
                $this->info('   ✓ GBP prices cached successfully!');
                $successCount++;
            } else {
                $this->error('   ✗ Failed to cache GBP prices');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error caching GBP prices: ' . $e->getMessage());
        }
        $this->newLine();

        // Cache AED prices
        $this->info('4. Caching AED prices...');
        try {
            $data = $egratesService->getAedPrices();
            if ($data) {
                $this->info('   ✓ AED prices cached successfully!');
                $successCount++;
            } else {
                $this->error('   ✗ Failed to cache AED prices');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error caching AED prices: ' . $e->getMessage());
        }
        $this->newLine();

        // Cache gold prices
        $this->info('5. Caching gold prices...');
        try {
            $data = $egratesService->getGoldPrices();
            if ($data) {
                $this->info('   ✓ Gold prices cached successfully!');
                $successCount++;
            } else {
                $this->error('   ✗ Failed to cache gold prices');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error caching gold prices: ' . $e->getMessage());
        }
        $this->newLine();

        // Cache banks data
        $this->info('6. Caching banks data...');
        try {
            $data = $egratesService->getBanks();
            if ($data) {
                $this->info('   ✓ Banks data cached successfully!');
                $successCount++;
            } else {
                $this->error('   ✗ Failed to cache banks data');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error caching banks data: ' . $e->getMessage());
        }
        $this->newLine();

        // Summary
        if ($successCount === $totalApis) {
            $this->info("🎉 All {$totalApis} APIs cached successfully!");
            $this->info('Data cached forever (until you run this command again)');
            return 0;
        } elseif ($successCount > 0) {
            $this->warn("⚠️  {$successCount}/{$totalApis} APIs cached successfully");
            $this->warn('Some APIs failed to cache');
            return 1;
        } else {
            $this->error("❌ All {$totalApis} APIs failed to cache");
            return 1;
        }
    }
}
