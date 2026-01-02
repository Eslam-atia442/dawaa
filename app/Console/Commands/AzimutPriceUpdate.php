<?php

namespace App\Console\Commands;

use App\Models\GoldFund;
use App\Models\GoldFundPriceLog;
use App\Enums\PriceDirectionEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AzimutPriceUpdate extends Command
{
    protected $signature = 'update:azimut-update-price {goldFundId}';

    protected $description = 'Update gold fund price';

    public function handle(){
        $goldFundId = $this->argument('goldFundId');

        $goldFund = GoldFund::find($goldFundId);

        if (!$goldFund) {
            $this->error("Gold fund with ID {$goldFundId} not found.");
            return 1;
        }

        if (!$goldFund->link) {
            $this->error("Gold fund {$goldFundId} has no API link configured.");
            return 1;
        }

        $this->info('Start: ' . ($goldFund->name ?? ('Fund #' . $goldFundId)));

        $link = $goldFund->link;

        $response = Http::get($link);

        if (!$response->successful()) {
            $this->error("Failed to fetch data from API: {$link}");
            return 1;
        }

        $data = $response->json()['data'] ?? null;

        if (!$data || !isset($data['graph'])) {
            $this->error("Invalid API response format - missing graph data.");
            return 1;
        }

        $prices = $data['graph'];

        try {
            DB::beginTransaction();

            $existingPricesCount = $goldFund->priceLogs()->count();
            $newPricesCount      = count($prices);

            $this->info("Existing prices: {$existingPricesCount}");
            $this->info("New prices from API: {$newPricesCount}");

            if ($existingPricesCount < $newPricesCount) {
                $pricesToAdd = $newPricesCount - $existingPricesCount;
                $this->info("Adding {$pricesToAdd} new price entries...");

                $newPrices = array_slice($prices, $existingPricesCount);

                foreach ($newPrices as $priceData) {
                    $timestamp = $priceData[0];
                    $price     = $priceData[1];

                    // Convert timestamp to seconds and create Carbon instance with UTC timezone
                    $timestampInSeconds = $timestamp / 1000;
                    $changedAt = Carbon::createFromTimestamp($timestampInSeconds, 'UTC');

                    // Validate the datetime
                    if (!$changedAt || $changedAt->year < 1970 || $changedAt->year > 2030) {
                        $this->warn("Invalid timestamp {$timestamp} for price {$price}, skipping...");
                        continue;
                    }

                    $previousPriceLog = $goldFund->priceLogs()->orderBy('changed_at', 'desc')->first();
                    $previousPrice    = $previousPriceLog ? $previousPriceLog->price : 0;

                    try {
                        GoldFundPriceLog::create([
                            'gold_fund_id'   => $goldFund->id,
                            'previous_price' => $previousPrice,
                            'price'          => $price,
                            'changed_at'     => $changedAt->format('Y-m-d H:i:s'),
                            'direction'      => $price > $previousPrice ? PriceDirectionEnum::up : ($price < $previousPrice ? PriceDirectionEnum::down : PriceDirectionEnum::same),
                        ]);

                        $this->info("Added price: {$price} at {$changedAt->format('Y-m-d H:i:s')}");
                    } catch (\Exception $e) {
                        $this->error("Failed to insert price log for timestamp {$timestamp}: " . $e->getMessage());
                        continue;
                    }
                }

                $this->info("Successfully added {$pricesToAdd} new price entries.");

                $latestPrice = end($newPrices)[1];
                $goldFund->update(['unit_price' => $latestPrice]);
                $this->info("Updated gold fund unit_price to: {$latestPrice}");
            } else {
                $this->info("No new prices to add. Existing count ({$existingPricesCount}) is >= API count ({$newPricesCount}).");
            }

            DB::commit();
            $this->info('End: ' . ($goldFund->name ?? ('Fund #' . $goldFundId)));
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error occurred while updating prices: " . $e->getMessage());
            return 1;
        }
    }
}
