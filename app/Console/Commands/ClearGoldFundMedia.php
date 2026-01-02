<?php

namespace App\Console\Commands;

use App\Models\GoldFund;
use Illuminate\Console\Command;

class ClearGoldFundMedia extends Command
{
    protected $signature = 'goldfund:clear-media {goldFundId?} {--all : Clear media for all gold funds}';

    protected $description = 'Delete all Spatie Media Library files for GoldFund (image, files collections)';

    public function handle(){
        $goldFundId = $this->argument('goldFundId');
        $forAll     = $this->option('all');

        if (!$forAll && !$goldFundId) {
            $this->error('Please provide a goldFundId or use --all');
            return self::FAILURE;
        }

        if ($forAll) {
            $this->info('Clearing media for ALL gold funds...');
            GoldFund::query()->chunkById(200, function ($funds){
                foreach ($funds as $fund) {
                    $this->clearMediaForFund($fund);
                }
            });
            $this->info('Done clearing media for all gold funds.');
            return self::SUCCESS;
        }

        $fund = GoldFund::find($goldFundId);
        if (!$fund) {
            $this->error("Gold fund with ID {$goldFundId} not found.");
            return self::FAILURE;
        }

        $this->clearMediaForFund($fund);
        return self::SUCCESS;
    }

    private function clearMediaForFund(GoldFund $fund): void
    {
        $name = is_array($fund->name) ? ($fund->name['en'] ?? reset($fund->name)) : $fund->name;
        $this->info('Start clearing: ' . ($name ?: ('Fund #' . $fund->id)));

        try {
            // Clear specific collections if they exist
            if ($fund->hasMedia('image')) {
                $fund->clearMediaCollection('image');
            }
            if ($fund->hasMedia('files')) {
                $fund->clearMediaCollection('files');
            }
        } catch (\Throwable $t) {
            $this->error('Error clearing media for fund ' . $fund->id . ': ' . $t->getMessage());
        }

        $this->info('End clearing: ' . ($name ?: ('Fund #' . $fund->id)));
    }
}



