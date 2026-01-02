<?php

namespace App\Console\Commands;

use App\Services\EgratesApiService;
use Illuminate\Console\Command;

class CacheEgratesCurrenciesCommand extends Command
{
	protected $signature = 'egrates:cache-currencies {codes*? : Currency codes like USD EUR GBP AED}';
	protected $description = 'Cache multiple currency prices from Egrates API forever (refresh on demand)';

	public function handle(EgratesApiService $egratesService)
	{
		$codes = $this->argument('codes');
		if (empty($codes)) {
			$codes = (array) config('services.egrates.currencies', ['USD', 'EUR', 'GBP', 'AED']);
		}

		$codes = array_values(array_unique(array_map(fn ($c) => strtoupper(trim($c)), $codes)));

		$this->info('Caching currency prices: ' . implode(', ', $codes));
		$this->newLine();

		$successCount = 0;
		$total = count($codes);

		foreach ($codes as $idx => $code) {
			$this->info(($idx + 1) . ". Caching {$code} prices...");
			try {
				$data = $egratesService->getCurrencyPrices($code);
				if ($data) {
					$this->info("   ✓ {$code} prices cached successfully!");
					$successCount++;
				} else {
					$this->error("   ✗ Failed to cache {$code} prices");
				}
			} catch (\Exception $e) {
				$this->error("   ✗ Error caching {$code} prices: " . $e->getMessage());
			}
			$this->newLine();
		}

		if ($successCount === $total) {
			$this->info("🎉 All {$total} currencies cached successfully!");
			$this->info('Data cached forever (until you run this command again)');
			return 0;
		}

		if ($successCount > 0) {
			$this->warn("⚠️  {$successCount}/{$total} currencies cached successfully");
			return 1;
		}

		$this->error("❌ All {$total} currencies failed to cache");
		return 1;
	}
}


