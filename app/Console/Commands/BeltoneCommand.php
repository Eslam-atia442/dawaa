<?php

namespace App\Console\Commands;

use App\Models\GoldFund;
use App\Models\GoldFundPriceLog;
use App\Enums\PriceDirectionEnum;
use App\Enums\ScrapeTypeEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeltoneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:beltone-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all beltone funds and update their prices';

    /**
     * Execute the console command.
     */
    public function handle(){
        $response = Http::get('https://api.beltoneholding.com/api/settings/get_asset_data?lang=ar');
        $data     = collect($response->json()['data']);
        $allFunds = collect();
        foreach ($data as $category) {
            foreach ($category['boxData'] as $fund) {
                $allFunds->push([
                    'category_id'    => $category['id'],
                    'category_title' => $category['title'],
                    'fund_data'      => $fund
                ]);
            }
        }
        $allFundsFlat = $data->flatMap(function ($category){
            return collect($category['boxData'])->map(function ($fund) use ($category){
                $lastUpdate = null;
                if (isset($fund['Last_upate']) && !empty($fund['Last_upate'])) {
                    try {
                        $lastUpdate = \Carbon\Carbon::createFromFormat('d-F-Y', $fund['Last_upate'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            $lastUpdate = \Carbon\Carbon::parse($fund['Last_upate'])->format('Y-m-d');
                        } catch (\Exception $e2) {
                            $lastUpdate = $fund['Last_upate'];
                        }
                    }
                }

                return array_merge($fund, [
                    'category_id'    => $category['id'],
                    'category_title' => $category['title'],
                    'Last_upate'     => $lastUpdate,
                ]);
            });
        });
        foreach ($allFundsFlat as $fund) {
            $this->info('Start: ' . $fund['Fund_name']);
            $goldFund = GoldFund::firstOrCreate(
                [
                    'scrape_id'   => $fund['id'],
                    'scrape_type' => ScrapeTypeEnum::beltone->value
                ],
                [
                    'name'       => ['en' => $fund['Fund_name'], 'ar' => $fund['Fund_name']],
                    'unit_price' => $fund['Fund_price'],
                    'link'       => 'https://api.beltoneholding.com/api/settings/get_asset_data'
                ]
            );

            info('updating price for ' . $fund['Fund_name']);

            if ($goldFund->unit_price != $fund['Fund_price']) {
                try {
                    DB::beginTransaction();

                    $previousPrice = $goldFund->unit_price;
                    $newPrice      = $fund['Fund_price'];
                    $currentTime   = Carbon::now();

                    // Create price log entry
                    GoldFundPriceLog::create([
                        'gold_fund_id'   => $goldFund->id,
                        'previous_price' => $previousPrice,
                        'price'          => $newPrice,
                        'changed_at'     => $currentTime,
                        'direction'      => $newPrice > $previousPrice ? PriceDirectionEnum::up : ($newPrice < $previousPrice ? PriceDirectionEnum::down : PriceDirectionEnum::same),
                    ]);

                    // Update the gold fund's unit price
                    $goldFund->update(['unit_price' => $newPrice]);

                    $this->info("Updated price for {$goldFund->name}: {$previousPrice} -> {$newPrice}");

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Failed to update price for {$goldFund->name}: " . $e->getMessage());
                }
            }

//            if (!$goldFund->hasMedia('files')) {
//                $goldFund->addMediaFromUrl($fund['file'])->toMediaCollection('files');
//            }

            $this->info('End: ' . $fund['Fund_name']);
        }
    }
}
