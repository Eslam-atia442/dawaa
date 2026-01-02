<?php

namespace App\Console\Commands;

use App\Services\EgratesApiService;
use Illuminate\Console\Command;

class CacheEgratesEurPricesCommand extends Command
{
    protected $signature = 'egrates:cache-eur-prices';
    protected $description = 'Cache EUR prices from Egrates API';

    public function handle(EgratesApiService $egratesService)
    {
        $this->info('Fetching and caching EUR prices from Egrates API...');

        try {
            $data = $egratesService->getEurPrices();

            if ($data) {
                $this->info('EUR prices cached successfully!');
                $this->line('Data cached forever (until you run this command again)');

                if ($this->option('verbose')) {
                    $this->line('Response data: ' . json_encode($data, JSON_PRETTY_PRINT));
                }
            } else {
                $this->error('Failed to fetch EUR prices from API');
                return 1;
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}


