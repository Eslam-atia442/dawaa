<?php

namespace App\Console\Commands;

use App\Enums\ScrapeTypeEnum;
use App\Models\GoldFund;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class AzimutCommand extends Command
{
    protected $signature   = 'update:azimut-list';
    protected $description = 'Get all azimut funds and update their prices';

    public function handle(){
        ini_set('memory_limit', '-1');

        $this->info("Processing Azimut funds...");

        try {
            $response = Http::retry(3, 500)->timeout(30)->get('https://api.azimut.eg/api/list/funds');

            if (!$response->successful()) {
                $this->error('Failed to fetch funds from API');
                return self::FAILURE;
            }

            $data = $response->json();

            if (!isset($data['data']) || !is_array($data['data'])) {
                $this->error('Invalid API response format');
                return self::FAILURE;
            }

            $funds = $data['data'];

            $failures = 0;
            foreach ($funds as $item) {
                try {
//                    if (isset($item['subscription'])) {
//                       info($item['subscription']);
//                    }
                    $this->processFund($item);
                } catch (\Throwable $t) {
                    $failures++;
                    info('Azimut item processing failed: ' . $t->getMessage());
                }
                gc_collect_cycles();
            }

            if ($failures > 0) {
                $this->warn("Completed with {$failures} item failure(s)");
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Command failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function processFund(array $item): void{


        // Basic validations for required keys
        if (!isset($item['name'], $item['slug'])) {
            info('Skipping fund: missing required keys');
            return;
        }

        $name     = ['en' => $item['name'], 'ar' => $item['name']];
        $link     = 'https://api.azimut.eg/api/list/funds/' . $item['slug'];
        $logo     = $item['logo_web'] ?? null;
        $scrapeId = $item['slug'];

        // get $link data
        $response = Http::retry(3, 500)->timeout(30)->get($link);
        $data     = $response->successful() ? $response->json() : null;
        $files    = isset($data['data']['files']) && is_array($data['data']['files']) ? $data['data']['files'] : [];

        $goldFund = GoldFund::firstOrCreate(
            [
                'scrape_id'   => $scrapeId,
                'scrape_type' => ScrapeTypeEnum::azimut->value,
            ],
            [
                'name' => $name,
                'link' => $link,

            ]

        );

//        if ($logo && !$goldFund->hasMedia('image')) {
//            try {
//                $goldFund->addMediaFromUrl($logo)->toMediaCollection('image');
//            } catch (\Throwable $t) {
//                info('Failed to attach image for ' . ($goldFund->name ?? $scrapeId) . ': ' . $t->getMessage());
//            }
//        }

//        if (!$goldFund->hasMedia('files')) {
//            foreach ($files as $file) {
//                try {
//                    if (isset($file['download_link'])) {
//                        $goldFund->addMediaFromUrl($file['download_link'])->toMediaCollection('files');
//                    }
//                } catch (\Throwable $t) {
//                    info('Failed to attach file for ' . ($goldFund->name ?? $scrapeId) . ': ' . $t->getMessage());
//                }
//            }
//        }

        info('updating price for ' . $goldFund->name);

        try {
            Artisan::call('update:azimut-update-price', ['goldFundId' => $goldFund->id]);
        } catch (\Throwable $t) {
            info('Failed to update price for ' . ($goldFund->name ?? $scrapeId) . ': ' . $t->getMessage());
        }

    }

}
