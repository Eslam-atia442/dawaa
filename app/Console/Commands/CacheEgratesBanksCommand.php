<?php

namespace App\Console\Commands;

use App\Services\EgratesApiService;
use Illuminate\Console\Command;

class CacheEgratesBanksCommand extends Command
{
    protected $signature = 'egrates:cache-banks';
    protected $description = 'Cache banks data from Egrates API';

    public function handle(EgratesApiService $egratesService)
    {
        $this->info('Fetching and caching banks data from Egrates API...');

        try {
            $data = $egratesService->getBanks();

            if ($data) {
                $this->info('Banks data cached successfully!');
                $this->line('Data cached forever (until you run this command again)');
                
                if ($this->option('verbose')) {
                    $this->line('Response data: ' . json_encode($data, JSON_PRETTY_PRINT));
                }
            } else {
                $this->error('Failed to fetch banks data from API');
                return 1;
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
