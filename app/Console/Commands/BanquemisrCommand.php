<?php

namespace App\Console\Commands;

use App\Enums\PriceDirectionEnum;
use App\Enums\ScrapeTypeEnum;
use App\Models\GoldFund;
use App\Models\GoldFundPriceLog;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class BanquemisrCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:banquemisr-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the banquemisr funds and their prices';

    /**
     * Execute the console command.
     */
    public function handle(){
        try {
            $client = HttpClient::create([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0 Safari/537.36',
                    'Accept-Language' => 'ar,en;q=0.9',
                ],
                'verify_peer' => false,
                'verify_host' => false,
            ]);

            $browser = new HttpBrowser($client);
            $crawler = $browser->request('GET', 'https://www.banquemisr.com/ar-EG/Home/CAPITAL-MARKETS/Mutual-funds');

            $html = $crawler->html();

            if (!str_contains($html, '<table')) {
                // Log the content for debugging
                \Log::warning('BanqueMisr page missing table', ['html_snippet' => substr($html, 0, 500)]);
            }

            $values = $crawler->filter('.table-responsive tbody tr')->each(function ($tr) {
                $name  = $tr->filter('td:nth-child(2) a')->count() ? trim($tr->filter('td:nth-child(2) a')->text()) : null;
                $price = $tr->filter('td:nth-child(3)')->count() ? trim($tr->filter('td:nth-child(3)')->text()) : null;
                return compact('name', 'price');
            });

            foreach ($values as $item) {
                if (!$item['name'] || !$item['price']) {
                    continue;
                }

                try {
                    $goldFund = GoldFund::firstOrCreate(
                        [
                            'scrape_id'   => $item['scrape_id'],
                            'scrape_type' => ScrapeTypeEnum::banquemisr->value
                        ],
                        [
                            'name'       => ['en' => $item['name'], 'ar' => $item['name']],
                            'link'       => $item['link'],
                            'unit_price' => $item['price'],
                            'is_active'  => false,
                        ]
                    );
                    $this->info('updating price for ' . $item['name']);
                    info('updating price for ' . $item['name']);

                    if ($goldFund->unit_price != $item['price']) {
                        GoldFundPriceLog::create([
                            'gold_fund_id'   => $goldFund->id,
                            'previous_price' => $goldFund->unit_price,
                            'price'          => $item['price'],
                            'direction'      => $item['price'] > $goldFund->unit_price ? PriceDirectionEnum::up->value : PriceDirectionEnum::down->value,
                            'changed_at'     => now(),
                        ]);
                        $goldFund->update(['unit_price' => $item['price']]);
                    }
                } catch (\Throwable $t) {
                    info('Failed to update price for ' . $item['name'] . ': ' . $t->getMessage());
                }
            }

            return self::SUCCESS;
        } catch (\Throwable $t) {
            info('Failed to scrape Banquemisr data: ' . $t->getMessage());
            return self::FAILURE;
        }
    }
}
