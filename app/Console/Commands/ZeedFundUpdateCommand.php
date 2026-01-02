<?php

namespace App\Console\Commands;

use App\Enums\PriceDirectionEnum;
use App\Models\GoldFund;
use App\Models\GoldFundPriceLog;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class ZeedFundUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateZeedByScrapId {scrape_id : The scrape ID of the fund to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a specific ZEED fund price and historical chart data by scrape ID';

    /**
     * Execute the console command.
     */
    public function handle(){
        $this->info('Starting individual ZEED fund update...');
        $scrapeId = $this->argument('scrape_id');
        $this->info('Looking for fund with scrape_id: ' . $scrapeId);

        try {
            $goldFund = GoldFund::where('scrape_id', $scrapeId)->first();
            $this->info('Database query completed');

            if (!$goldFund) {
                $this->info("Fund with scrape_id {$scrapeId} not found. Attempting to create fund by scraping main page...");

                // Try to get fund data from main funds page
                $browser = new HttpBrowser(HttpClient::create([
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
                ]));

                try {
                    $crawler = $browser->request('GET', 'https://zeed.tech/funds/');
                    $fundRow = $crawler->filter("tr.wcpt-row[data-wcpt-product-id='{$scrapeId}']");

                    if ($fundRow->count() === 0) {
                        $this->error("Could not find fund data for scrape_id: {$scrapeId} on main page");
                        return self::FAILURE;
                    }

                    // Extract fund data from the row
                    $issuer = null;
                    $issuerImg = $fundRow->filter('td.wcpt-cell:nth-child(1) img');
                    if ($issuerImg->count()) {
                        $issuer = $issuerImg->attr('alt') ?: $issuerImg->attr('title');
                    }

                    $fundName = null;
                    $link = null;
                    $fundLink = $fundRow->filter('td.wcpt-cell:nth-child(2) a');
                    if ($fundLink->count()) {
                        $fundName = trim($fundLink->text());
                        $link = $fundLink->attr('href');
                    }

                    $lastPrice = null;
                    $priceElement = $fundRow->filter('td.wcpt-cell:nth-child(3) .wcpt-amount');
                    if ($priceElement->count()) {
                        $lastPrice = trim($priceElement->text());
                    }

                    $ytd = null;
                    $ytdElement = $fundRow->filter('td.wcpt-cell:nth-child(4) .wcpt-custom-field');
                    if ($ytdElement->count()) {
                        $ytd = trim($ytdElement->text());
                    }

                    $oneYear = null;
                    $oneYearElement = $fundRow->filter('td.wcpt-cell:nth-child(5) .wcpt-custom-field');
                    if ($oneYearElement->count()) {
                        $oneYear = trim($oneYearElement->text());
                    }

                    $sinceInception = null;
                    $sinceInceptionElement = $fundRow->filter('td.wcpt-cell:nth-child(6) .wcpt-custom-field');
                    if ($sinceInceptionElement->count()) {
                        $sinceInception = trim($sinceInceptionElement->text());
                    }

                    $category = null;
                    $categoryElement = $fundRow->filter('td.wcpt-cell:nth-child(7) .wcpt-category');
                    if ($categoryElement->count()) {
                        $category = trim($categoryElement->text());
                    }

                    $type = null;
                    $typeElement = $fundRow->filter('td.wcpt-cell:nth-child(8) .wcpt-attribute-term');
                    if ($typeElement->count()) {
                        $type = trim($typeElement->text());
                    }

                    $risk = null;
                    $riskElement = $fundRow->filter('td.wcpt-cell:nth-child(9) .wcpt-attribute-term');
                    if ($riskElement->count()) {
                        $risk = trim($riskElement->text());
                    }

                    $cleanPrice = preg_replace('/[^0-9.]/', '', str_replace(',', '', $lastPrice));

                    $this->info("Creating fund: {$fundName}");

                    $goldFund = GoldFund::create([
                        'scrape_id' => $scrapeId,
                        'scrape_type' => ScrapeTypeEnum::zeed->value,
                        'name' => ['en' => $fundName ?: "Fund {$scrapeId}", 'ar' => $fundName ?: "Fund {$scrapeId}"],
                        'link' => $link,
                        'unit_price' => $cleanPrice ?: 0,
                        'issuer' => $issuer,
                        'ytd' => $ytd,
                        'one_year' => $oneYear,
                        'since_inception' => $sinceInception,
                        'category' => $category,
                        'fund_type' => $type,
                        'risk_level' => $risk,
                    ]);

                    $this->info("Fund created successfully with ID: {$goldFund->id}");

                } catch (\Exception $e) {
                    $this->error("Failed to create fund: " . $e->getMessage());
                    return self::FAILURE;
                }
            }

            if (!$goldFund->link) {
                $this->error("Fund has no link to scrape from");
                return self::FAILURE;
            }

            $this->info("Processing fund: {$goldFund->name} (ID: {$goldFund->id})");
            $this->info("Scraping from: {$goldFund->link}");

            $browser = new HttpBrowser(HttpClient::create([
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
            ]));
            $crawler = $browser->request('GET', $goldFund->link);

            $currentPrice = null;
            $priceElement = $crawler->filter('.price');
            if ($priceElement->count()) {
                $priceText = trim($priceElement->text());
                $cleanPrice = preg_replace('/[^0-9.,]/', '', $priceText);
                $currentPrice = (float) str_replace(',', '', $cleanPrice);
                $this->info("Found current price: {$currentPrice}");
            } else {
                $this->error("Could not find price element on the page");
                return self::FAILURE;
            }

            $priceChanged = false;
            if ($goldFund->unit_price != $currentPrice) {
                $this->info("Price changed from {$goldFund->unit_price} to {$currentPrice}");

                GoldFundPriceLog::create([
                    'gold_fund_id'   => $goldFund->id,
                    'previous_price' => $goldFund->unit_price,
                    'price'          => $currentPrice,
                    'direction'      => $currentPrice > $goldFund->unit_price ? PriceDirectionEnum::up->value : PriceDirectionEnum::down->value,
                    'changed_at'     => now(),
                ]);

                $goldFund->update(['unit_price' => $currentPrice]);
                $priceChanged = true;
            } else {
                $this->info("Price unchanged: {$currentPrice}");
            }

            $historicalPrices = [];

            $scriptElements = $crawler->filter('script');
            $scriptElements->each(function ($script) use (&$historicalPrices) {
                $scriptContent = $script->text();
                if (strpos($scriptContent, 'var visualizer =') !== false) {
                    if (preg_match('/var visualizer = ({.+?});/', $scriptContent, $vizMatch)) {
                        $visualizerConfig = json_decode($vizMatch[1], true);
                        if ($visualizerConfig && isset($visualizerConfig['charts'])) {
                            foreach ($visualizerConfig['charts'] as $chartId => $chartData) {
                                if (isset($chartData['data'])) {
                                    $chartPoints = $chartData['data'];
                                    if (count($chartPoints) > 0) {
                                        $historicalPrices = array_merge($historicalPrices, $chartPoints);
                                    }
                                }
                            }
                        }
                    }
                }

                if (preg_match_all('/\[(\d{10,13}),\s*([0-9]+\.?[0-9]*)\]/', $scriptContent, $matches)) {
                    $dataPoints = [];
                    for ($i = 0; $i < count($matches[1]); $i++) {
                        $timestamp = (int) $matches[1][$i];
                        $price = (float) $matches[2][$i];
                        $dataPoints[] = [$timestamp, $price];
                    }
                    if (count($dataPoints) > 3) {
                        $historicalPrices = array_merge($historicalPrices, $dataPoints);
                    }
                }

                if (preg_match('/["\']data["\']\s*:\s*(\[[^\]]*\])/s', $scriptContent, $jsonMatch)) {
                    $jsonData = json_decode($jsonMatch[1], true);
                    if ($jsonData && is_array($jsonData) && count($jsonData) > 0) {
                        $isChartData = true;
                        foreach (array_slice($jsonData, 0, min(3, count($jsonData))) as $point) {
                            if (!is_array($point) || count($point) < 2 || !is_numeric($point[0]) || !is_numeric($point[1])) {
                                $isChartData = false;
                                break;
                            }
                        }
                        if ($isChartData) {
                            $historicalPrices = array_merge($historicalPrices, $jsonData);
                        }
                    }
                }

                if (strpos($scriptContent, 'Highcharts') !== false) {
                    if (preg_match_all('/data\s*:\s*(\[[^\]]*\])/s', $scriptContent, $highchartsMatches)) {
                        foreach ($highchartsMatches[1] as $dataStr) {
                            $chartData = json_decode($dataStr, true);
                            if ($chartData && is_array($chartData) && count($chartData) > 0) {
                                $historicalPrices = array_merge($historicalPrices, $chartData);
                            }
                        }
                    }
                }
            });

            $chartElements = $crawler->filter('[data-chart], [data-series], [data-visualizer], [id*="chart"], [class*="chart"]');
            $chartElements->each(function ($element) use (&$historicalPrices) {
                $dataAttrs = ['data-chart', 'data-series', 'data-visualizer', 'data-points'];
                foreach ($dataAttrs as $attr) {
                    $value = $element->attr($attr);
                    if ($value) {
                        $jsonData = json_decode($value, true);
                        if ($jsonData && is_array($jsonData)) {
                            if (isset($jsonData['data']) && is_array($jsonData['data'])) {
                                $historicalPrices = array_merge($historicalPrices, $jsonData['data']);
                            } elseif (count($jsonData) > 0 && is_array($jsonData[0])) {
                                $historicalPrices = array_merge($historicalPrices, $jsonData);
                            }
                        }
                    }
                }
            });

            $svgElements = $crawler->filter('svg');
            $svgElements->each(function ($svg) use (&$historicalPrices) {
                $svgContent = $svg->html();

                if (preg_match_all('/(\d{10,13}),([0-9]+\.?[0-9]*)/', $svgContent, $svgMatches)) {
                    $svgDataPoints = [];
                    for ($i = 0; $i < count($svgMatches[1]); $i++) {
                        $timestamp = (int) $svgMatches[1][$i];
                        $price = (float) $svgMatches[2][$i];
                        $svgDataPoints[] = [$timestamp, $price];
                    }
                    if (count($svgDataPoints) > 0) {
                        $historicalPrices = array_merge($historicalPrices, $svgDataPoints);
                    }
                }
            });

            $historicalPrices = array_unique($historicalPrices, SORT_REGULAR);
            usort($historicalPrices, function($a, $b) {
                return $a[0] <=> $b[0];
            });

            $this->info("Found " . count($historicalPrices) . " historical price points");

            $historicalPricesSaved = 0;
            $previousPrice = null;

            foreach ($historicalPrices as $pricePoint) {
                if (is_array($pricePoint) && count($pricePoint) >= 2) {
                    $dateStr = $pricePoint[0];
                    $price = (float) $pricePoint[1];

                    $date = null;
                    if (is_string($dateStr)) {
                        $parsedDate = date_parse($dateStr);
                        if ($parsedDate && $parsedDate['year'] && $parsedDate['month'] && $parsedDate['day']) {
                            $date = sprintf('%04d-%02d-%02d 12:00:00',
                                $parsedDate['year'],
                                $parsedDate['month'],
                                $parsedDate['day']
                            );
                        }
                    }

                    if ($date && $price > 0) {
                        $existingLog = GoldFundPriceLog::where('gold_fund_id', $goldFund->id)
                            ->where('price', $price)
                            ->whereDate('changed_at', date('Y-m-d', strtotime($date)))
                            ->first();

                        if (!$existingLog) {
                            $direction = PriceDirectionEnum::same->value;
                            if ($previousPrice !== null) {
                                if ($price > $previousPrice) {
                                    $direction = PriceDirectionEnum::up->value;
                                } elseif ($price < $previousPrice) {
                                    $direction = PriceDirectionEnum::down->value;
                                }
                            }

                            GoldFundPriceLog::create([
                                'gold_fund_id'   => $goldFund->id,
                                'previous_price' => $previousPrice ?? $price,
                                'price'          => $price,
                                'direction'      => $direction,
                                'changed_at'     => $date,
                            ]);
                            $historicalPricesSaved++;

                            $previousPrice = $price;
                        }
                    }
                }
            }

            $this->info("Saved {$historicalPricesSaved} historical price points");

            if ($priceChanged) {
                $this->info("✅ Fund price updated successfully");
            } else {
                $this->info("ℹ️  Fund price was already up to date");
            }

            return self::SUCCESS;

        } catch (\Throwable $t) {
            $this->error('Failed to update fund price: ' . $t->getMessage());
            return self::FAILURE;
        }
    }
}
