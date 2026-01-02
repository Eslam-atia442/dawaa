<?php

namespace App\Console\Commands;

use App\Models\Gold;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class UpdateGoldHistoryCommand extends Command
{
    protected $signature = 'update:gold-history';

    protected $description = 'Update gold price history from goldpricez.com';

    public function handle()
    {
        try {
            $this->info('Starting gold history update...');

            $browser = new HttpBrowser(HttpClient::create());
            $crawler = $browser->request('GET', 'https://goldpricez.com/gold/history/egp/years-3');

            $today = Carbon::now()->format('d-M-Y');

            $rows = $crawler->filter('.tb tr')->each(function ($row) use ($today) {
                $cells = $row->filter('td');
                if ($cells->count() == 2) {
                    $date  = trim($cells->eq(0)->text());
                    $price = trim($cells->eq(1)->text());

                    if (str_contains($date, 'Current Price')) {
                        $date = $today;
                    }

                    $price = str_replace('EGP', '', $price);
                    $price = (float)trim($price);

                    try {
                        $createdAt = Carbon::createFromFormat('d-M-Y', $date);
                        if (!$createdAt || $createdAt->year < 1970 || $createdAt->year > 2100) {
                            return null;
                        }
                        $createdAt = $createdAt->startOfDay();
                    } catch (\Exception $e) {
                        return null;
                    }

                    return [
                        'created_at' => $createdAt,
                        'price'      => $price,
                    ];
                }
            });

            $rows = array_filter($rows);
            $createdCount = 0;
            $skippedCount = 0;

            foreach ($rows as $row) {
                if (!$row || !isset($row['created_at']) || !isset($row['price'])) {
                    $skippedCount++;
                    continue;
                }

                $price = $row['price'];
                $createdAt = $row['created_at'];

                if (!($createdAt instanceof Carbon)) {
                    $skippedCount++;
                    continue;
                }

                if ($createdAt->year < 1970 || $createdAt->year > 2100) {
                    $skippedCount++;
                    continue;
                }

                $dateStart = $createdAt->copy()->startOfDay();

                $dateString = $dateStart->format('Y-m-d H:i:s');
                if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateString)) {
                    $skippedCount++;
                    continue;
                }

                try {
                    $existing = Gold::whereDate('created_at', $dateStart->format('Y-m-d'))->exists();

                    if (!$existing) {
                        $gold = new Gold();
                        $gold->timestamps = false;
                        $gold->price = $price;
                        $gold->created_at = $dateStart;
                        $gold->updated_at = Carbon::now();
                        $gold->save();
                        $createdCount++;
                    } else {
                        $skippedCount++;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to insert gold price', [
                        'date' => $dateString,
                        'price' => $price,
                        'error' => $e->getMessage()
                    ]);
                    $skippedCount++;
                    continue;
                }
            }

            $this->info("Gold history update completed. Created: {$createdCount}, Skipped: {$skippedCount}");
            return self::SUCCESS;
        } catch (\Throwable $t) {
            $this->error('Failed to update gold history: ' . $t->getMessage());
            Log::error('Failed to update gold history', [
                'error' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }
}




