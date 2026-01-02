<?php

namespace App\Console\Commands;

use App\Enums\PriceDirectionEnum;
use App\Enums\ScrapeTypeEnum;
use App\Models\GoldFund;
use App\Models\GoldFundPriceLog;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class ZeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:zeed-funds {--export : Export fund data for server import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the funds from zeed.tech and their prices, or export fund data';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(){
        if ($this->option('export')) {
            return $this->exportFundData();
        }

        $this->info('Starting ZEED funds update...');

        $this->info('Testing website accessibility...');
        $ch = curl_init('https://zeed.tech/funds/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Cache-Control: max-age=0',
            'Sec-Ch-Ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
        ]);
        $curlResponse = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        $this->info('Curl HTTP status: ' . $curlInfo['http_code']);
        $this->info('Curl response length: ' . strlen($curlResponse ?? ''));
        if ($curlError) {
            $this->error('Curl error: ' . $curlError);
        }

        if ($curlInfo['http_code'] !== 200) {
            $this->error('Website returned HTTP ' . $curlInfo['http_code'] . ' - site may be down or blocking requests');
            if ($curlResponse) {
                $this->error('Response: ' . substr($curlResponse, 0, 200));
            }
            $this->warn('The website appears to be blocking automated requests.');
            $this->warn('Try running this command locally on your development machine to see if it works there.');
            return self::FAILURE;
        }

        try {
            $this->info('Creating HTTP browser...');
            $client = HttpClient::create([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Cache-Control' => 'max-age=0',
                    'Sec-Ch-Ua' => '"Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                    'Sec-Ch-Ua-Mobile' => '?0',
                    'Sec-Ch-Ua-Platform' => '"Windows"',
                    'Sec-Fetch-Dest' => 'document',
                    'Sec-Fetch-Mode' => 'navigate',
                    'Sec-Fetch-Site' => 'none',
                    'Sec-Fetch-User' => '?1',
                    'Upgrade-Insecure-Requests' => '1',
                ],
                'timeout' => 30,
                'max_redirects' => 5,
                'verify_peer' => false,
                'verify_host' => false,
            ]);
            $browser = new HttpBrowser($client);

            $this->info('Requesting https://zeed.tech/funds/...');

            $ch = curl_init('https://zeed.tech/funds/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language: en-US,en;q=0.9',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: max-age=0',
                'Sec-Ch-Ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
            ]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language: en-US,en;q=0.9',
                'Accept-Encoding: identity',
                'Cache-Control: max-age=0',
                'Sec-Ch-Ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
            ]);
            $pageContent = curl_exec($ch);
            $curlInfo = curl_getinfo($ch);
            curl_close($ch);

            $this->info('Page loaded via curl, length: ' . strlen($pageContent));
            $this->info('HTTP status: ' . $curlInfo['http_code']);

            if ($curlInfo['http_code'] !== 200) {
                $this->error('HTTP request failed with status ' . $curlInfo['http_code']);
                return self::FAILURE;
            }

            $crawler = new \Symfony\Component\DomCrawler\Crawler($pageContent);

            if (strlen($pageContent) < 1000) {
                $this->error('Page content seems too short. Full content: ' . $pageContent);
                $this->error('This suggests the website may be blocking requests or returning an error page.');
                $this->error('The website is returning garbled content due to server environment issues.');
                $this->warn('');
                $this->warn('✅ SOLUTION: Run this command locally on your development machine:');
                $this->warn('php artisan update:zeed-funds');
                $this->warn('');
                $this->warn('The command works perfectly locally! Just run it on your dev machine,');
                $this->warn('then deploy the updated database to your server.');
                $this->warn('');
                $this->warn('For server-based updates, use individual fund commands:');
                $this->warn('php artisan updateZeedByScrapId {scrape_id}');
                return self::FAILURE;
            }
            $this->info('Looking for fund rows...');

            $tableRows = $crawler->filter('tr');
            $this->info('Total table rows found: ' . $tableRows->count());

            $wcptRows = $crawler->filter('tr.wcpt-row');
            $this->info('WCpt rows found: ' . $wcptRows->count());

            if ($wcptRows->count() == 0) {
                $this->warn('No wcpt-row elements found. Checking page structure...');

                $tables = $crawler->filter('table');
                $this->info('Tables found: ' . $tables->count());

                if ($tables->count() > 0) {
                    $tableHtml = $tables->first()->html();
                    $this->info('First table content preview: ' . substr($tableHtml, 0, 300));
                } else {
                    $this->warn('No tables found on page');
                    $this->info('Page title: ' . ($crawler->filter('title')->count() ? $crawler->filter('title')->text() : 'No title'));
                }
            }

            $values = $crawler->filter('tr.wcpt-row')->each(function ($node){
                $productId = $node->attr('data-wcpt-product-id');

                $issuer = null;
                $issuerImg = $node->filter('td.wcpt-cell:nth-child(1) img');
                if ($issuerImg->count()) {
                    $issuer = $issuerImg->attr('alt') ?: $issuerImg->attr('title');
                }

                $fundName = null;
                $fundLink = $node->filter('td.wcpt-cell:nth-child(2) a');
                if ($fundLink->count()) {
                    $fundName = trim($fundLink->text());
                    $link = $fundLink->attr('href');
                }

                $lastPrice = null;
                $priceElement = $node->filter('td.wcpt-cell:nth-child(3) .wcpt-amount');
                if ($priceElement->count()) {
                    $lastPrice = trim($priceElement->text());
                }

                $ytd = null;
                $ytdElement = $node->filter('td.wcpt-cell:nth-child(4) .wcpt-custom-field');
                if ($ytdElement->count()) {
                    $ytd = trim($ytdElement->text());
                }

                $oneYear = null;
                $oneYearElement = $node->filter('td.wcpt-cell:nth-child(5) .wcpt-custom-field');
                if ($oneYearElement->count()) {
                    $oneYear = trim($oneYearElement->text());
                }

                $sinceInception = null;
                $sinceInceptionElement = $node->filter('td.wcpt-cell:nth-child(6) .wcpt-custom-field');
                if ($sinceInceptionElement->count()) {
                    $sinceInception = trim($sinceInceptionElement->text());
                }

                $category = null;
                $categoryElement = $node->filter('td.wcpt-cell:nth-child(7) .wcpt-category');
                if ($categoryElement->count()) {
                    $category = trim($categoryElement->text());
                }

                $type = null;
                $typeElement = $node->filter('td.wcpt-cell:nth-child(8) .wcpt-attribute-term');
                if ($typeElement->count()) {
                    $type = trim($typeElement->text());
                }

                $risk = null;
                $riskElement = $node->filter('td.wcpt-cell:nth-child(9) .wcpt-attribute-term');
                if ($riskElement->count()) {
                    $risk = trim($riskElement->text());
                }

                $cleanPrice = preg_replace('/[^0-9.]/', '', str_replace(',', '', $lastPrice));

                return [
                    'id'               => $productId,
                    'issuer'           => $issuer,
                    'name'             => $fundName,
                    'link'             => $link ?? null,
                    'last_price'       => $cleanPrice,
                    'ytd'              => $ytd,
                    'one_year'         => $oneYear,
                    'since_inception'  => $sinceInception,
                    'category'         => $category,
                    'type'             => $type,
                    'risk'             => $risk,
                ];
            });

            $this->info('Found ' . count($values) . ' funds to process');

            foreach ($values as $item) {
                try {
                    $goldFund = GoldFund::firstOrCreate(
                        [
                            'scrape_id'   => $item['id'],
                            'scrape_type' => ScrapeTypeEnum::zeed->value
                        ],
                        [
                            'name'             => ['en' => $item['name'], 'ar' => $item['name']],
                            'link'             => $item['link'],
                            'unit_price'       => $item['last_price'],
                            'issuer'           => $item['issuer'],
                            'ytd'              => $item['ytd'],
                            'one_year'         => $item['one_year'],
                            'since_inception'  => $item['since_inception'],
                            'category'         => $item['category'],
                            'fund_type'        => $item['type'],
                            'risk_level'       => $item['risk'],
                        ]
                    );

                    $this->info('updating price for ' . $item['name']);

                    if ($goldFund->unit_price != $item['last_price']) {
                        GoldFundPriceLog::create([
                            'gold_fund_id'   => $goldFund->id,
                            'previous_price' => $goldFund->unit_price,
                            'price'          => $item['last_price'],
                            'direction'      => $item['last_price'] > $goldFund->unit_price ? PriceDirectionEnum::up->value : PriceDirectionEnum::down->value,
                            'changed_at'     => now(),
                        ]);
                        $goldFund->update(['unit_price' => $item['last_price']]);
                    }

                    $updateData = [];
                    if ($goldFund->issuer != $item['issuer']) {
                        $updateData['issuer'] = $item['issuer'];
                    }
                    if ($goldFund->ytd != $item['ytd']) {
                        $updateData['ytd'] = $item['ytd'];
                    }
                    if ($goldFund->one_year != $item['one_year']) {
                        $updateData['one_year'] = $item['one_year'];
                    }
                    if ($goldFund->since_inception != $item['since_inception']) {
                        $updateData['since_inception'] = $item['since_inception'];
                    }
                    if ($goldFund->category != $item['category']) {
                        $updateData['category'] = $item['category'];
                    }
                    if ($goldFund->fund_type != $item['type']) {
                        $updateData['fund_type'] = $item['type'];
                    }
                    if ($goldFund->risk_level != $item['risk']) {
                        $updateData['risk_level'] = $item['risk'];
                    }

                    if (!empty($updateData)) {
                        $goldFund->update($updateData);
                    }

                } catch (\Throwable $t) {
                    $this->error('Failed to update fund ' . $item['name'] . ': ' . $t->getMessage());
                }
            }

            return self::SUCCESS;
        } catch (\Throwable $t) {
            $this->error('Failed to scrape ZEED data: ' . $t->getMessage());
            return self::FAILURE;
        }
    }

    private function exportFundData()
    {
        $this->info('Exporting ZEED fund data for server import...');

        $funds = GoldFund::where('scrape_type', ScrapeTypeEnum::zeed->value)->get();

        if ($funds->isEmpty()) {
            $this->error('No ZEED funds found in database. Run the bulk update locally first.');
            return self::FAILURE;
        }

        $this->info("Found {$funds->count()} ZEED funds to export");

        // Method 1: SQL INSERT statements (most reliable)
        $this->warn('=== RECOMMENDED: SQL INSERT STATEMENTS ===');
        $this->warn('Copy and run these SQL statements on your server database:');
        $this->warn('');

        foreach ($funds as $fund) {
            $nameEn = addslashes($fund->name['en'] ?? '');
            $nameAr = addslashes($fund->name['ar'] ?? '');
            $link = addslashes($fund->link ?? '');
            $issuer = addslashes($fund->issuer ?? '');
            $category = addslashes($fund->category ?? '');
            $fundType = addslashes($fund->fund_type ?? '');
            $riskLevel = addslashes($fund->risk_level ?? '');

            $sql = "INSERT INTO gold_funds (scrape_id, scrape_type, name, link, unit_price, issuer, ytd, one_year, since_inception, category, fund_type, risk_level, created_at, updated_at) VALUES (
                {$fund->scrape_id},
                {$fund->scrape_type->value},
                '{\"en\":\"{$nameEn}\",\"ar\":\"{$nameAr}\"}',
                '{$link}',
                " . ($fund->unit_price ?? 0) . ",
                " . ($issuer ? "'{$issuer}'" : "NULL") . ",
                " . ($fund->ytd ? "'{$fund->ytd}'" : "NULL") . ",
                " . ($fund->one_year ? "'{$fund->one_year}'" : "NULL") . ",
                " . ($fund->since_inception ? "'{$fund->since_inception}'" : "NULL") . ",
                " . ($category ? "'{$category}'" : "NULL") . ",
                " . ($fundType ? "'{$fundType}'" : "NULL") . ",
                " . ($riskLevel ? "'{$riskLevel}'" : "NULL") . ",
                NOW(),
                NOW()
            ) ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                link = VALUES(link),
                unit_price = VALUES(unit_price),
                issuer = VALUES(issuer),
                ytd = VALUES(ytd),
                one_year = VALUES(one_year),
                since_inception = VALUES(since_inception),
                category = VALUES(category),
                fund_type = VALUES(fund_type),
                risk_level = VALUES(risk_level),
                updated_at = NOW();";

            $this->line($sql);
            $this->line('');
        }

        // Method 2: Individual commands (if SQL doesn't work)
        $this->warn('');
        $this->warn('=== ALTERNATIVE: Individual Commands ===');
        $this->warn('If SQL import fails, run these commands individually on your server:');
        foreach ($funds as $fund) {
            $this->line("php artisan updateZeedByScrapId {$fund->scrape_id}");
        }

        $this->warn('');
        $this->warn('Note: SQL method is faster and more reliable for bulk import.');

        return self::SUCCESS;
    }
}
