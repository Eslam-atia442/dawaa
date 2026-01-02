<?php

namespace App\Console\Commands;

use App\Services\EgratesApiService;
use Illuminate\Console\Command;

class CacheEgratesGoldPricesCommand extends Command
{
    protected $signature = 'egrates:cache-gold-prices';
    protected $description = 'Cache gold prices from Egrates API';

    public function handle(EgratesApiService $egratesService)
    {
        $this->info('Fetching and caching gold prices from Egrates API...');

        try {
            $data = $egratesService->getGoldPrices();

            if ($data) {
                $this->info('Gold prices cached successfully!');
                $this->line('Data cached forever (until you run this command again)');
                
                if ($this->option('verbose')) {
                    $this->line('Response data: ' . json_encode($data, JSON_PRETTY_PRINT));
                }
            } else {
                $this->error('Failed to fetch gold prices from API');
                return 1;
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
