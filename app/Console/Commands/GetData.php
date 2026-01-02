<?php

namespace App\Console\Commands;

use App\Services\EgratesApiService;
use Illuminate\Console\Command;

class GetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(EgratesApiService $egratesService)
    {
        $this->info('Fetching and caching banks data from Egrates API...');

        try {
            $data = $egratesService->getBanks();

            if ($data) {
                $this->info('Banks data cached successfully!');
                $this->line('Data cached for 1 hour');

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
