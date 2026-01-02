<?php

namespace App\Console\Commands;

use App\Enums\PriceDirectionEnum;
use App\Enums\ScrapeTypeEnum;
use App\Models\GoldFund;
use App\Models\GoldFundPriceLog;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class AfimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:afim-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the afim funds and their prices';

    /**
     * Execute the console command.
     */
    public function handle(){
        try {
            $browser = new HttpBrowser(HttpClient::create());
            $crawler = $browser->request('GET', 'https://afim.com.eg/public/index.php/investment');

            $values = $crawler->filter('.default-portfolio-item')->each(function ($node){
                $link  = $node->filter('a')->count() ? $node->filter('a')->attr('href') : null;
                $title = $node->filter('.info p')->count() ? trim($node->filter('.info p')->text()) : null;
                $price = $node->filter('.fundPrice span')->count() ? trim($node->filter('.fundPrice span')->text()) : null;

                $id = null;
                if ($link && preg_match('/get-service\/(\d+)/', $link, $matches)) {
                    $id = $matches[1];
                }

                $cleanPrice = preg_replace('/[^0-9.,]/', '', $price);

                return [
                    'id'    => $id,
                    'link'  => $link,
                    'title' => $title,
                    'price' => $cleanPrice,
                ];
            });

            foreach ($values as $item) {
                try {
                    $goldFund = GoldFund::firstOrCreate(
                        [
                            'scrape_id'   => $item['id'],
                            'scrape_type' => ScrapeTypeEnum::afim->value
                        ],
                        [
                            'name'       => ['en' => $item['title'], 'ar' => $item['title']],
                            'link'       => $item['link'],
                            'unit_price' => $item['price'],
                        ]
                    );
                    $this->info('updating price for ' . $item['title']);
                    info('updating price for ' . $item['title']);

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
                    info('Failed to update price for ' . $item['title'] . ': ' . $t->getMessage());
                }
            }

            return self::SUCCESS;
        } catch (\Throwable $t) {
            info('Failed to scrape AFIM data: ' . $t->getMessage());
            return self::FAILURE;
        }
    }
}
