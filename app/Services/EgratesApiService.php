<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EgratesApiService
{
    protected $baseUrl = 'https://egrates.com/api/v1';
    protected $token;
    protected $defaultCurrencies;

    public function __construct()
    {
        $this->token = config('services.egrates.token');
        $this->baseUrl = config('services.egrates.base_url', $this->baseUrl);
        $this->defaultCurrencies = array_values(array_unique(array_map(
            fn($c) => strtoupper(trim($c)),
            (array) config('services.egrates.currencies', ['USD', 'EUR', 'GBP', 'AED'])
        )));
    }

    /**
     * Get USD best prices and cache the response forever
     */
    public function getUsdPrices()
    {
        try {
            $response = Http::get("{$this->baseUrl}/prices/USD/best", [
                'token' => $this->token
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cacheData = [
                    'data' => $data,
                    'cached_at' => now()->toISOString(),
                    // forever cache: no expires_at
                ];
                Cache::forever('egrates_usd_prices', $cacheData);
                return $data;
            }

            Log::error('Egrates USD prices API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Egrates USD prices API exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get generic currency best prices and cache forever
     */
    public function getCurrencyPrices(string $currency , bool $force = false)
    {

        $currency = strtoupper($currency);
        if (!in_array($currency, $this->defaultCurrencies)) {
            return null;
        }
        $cacheKey = 'egrates_' . strtolower($currency) . '_prices';
        $cached = Cache::get($cacheKey);
        if ($cached && !$force) {
            return $cached['data'];
        }
        try {
            $currency = strtoupper($currency);
            $response = Http::get("{$this->baseUrl}/prices/{$currency}/best", [
                'token' => $this->token
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cacheData = [
                    'data' => $data,
                    'cached_at' => now()->toISOString(),
                ];
                Cache::forever($cacheKey, $cacheData);
                return $data;
            }

            Log::error('Egrates currency prices API failed', [
                'currency' => $currency,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Egrates currency prices API exception', [
                'currency' => $currency ?? 'UNKNOWN',
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get banks prices for a given currency
     */
    public function banksPrices(string $currency, bool $force = false)
    {
        $currency = strtoupper($currency);
        if (!in_array($currency, $this->defaultCurrencies)) {
            return null;
        }
        $cacheKey = 'egrates_' . strtolower($currency) . '_banks_prices';
        $cached = Cache::get($cacheKey);
        if ($cached && !$force) {
            return $cached['data'];
        }
        try {
            $currency = strtoupper($currency);
            $response = Http::get("{$this->baseUrl}/prices/{$currency}", [
                'token' => $this->token
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cacheKey = 'egrates_' . strtolower($currency) . '_banks_prices';
                $cacheData = [
                    'data' => $data,
                    'cached_at' => now()->toISOString(),
                ];
                Cache::forever($cacheKey, $cacheData);
                return $data;
            }

            Log::error('Egrates banks prices API failed', [
                'currency' => $currency,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Egrates currency prices API exception', [
                'currency' => $currency ?? 'UNKNOWN',
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getEurPrices()
    {
        return $this->getCurrencyPrices('EUR');
    }

    public function getGbpPrices()
    {
        return $this->getCurrencyPrices('GBP');
    }

    public function getAedPrices()
    {
        return $this->getCurrencyPrices('AED');
    }

    /**
     * Get cached currency prices without auto-refresh
     */
    public function getCachedCurrencyPrices(string $currency)
    {
        $currency = strtoupper($currency);
        return Cache::get('egrates_' . strtolower($currency) . '_prices');
    }

    /**
     * Get all cached currency prices for given list (defaults to USD, EUR, GBP, AED)
     * Returns only currencies that exist in cache; does not fetch
     */
    public function getAllCachedCurrencyPrices(array $currencies = null): array
    {
        $list = $currencies === null ? $this->defaultCurrencies : $currencies;
        $result = [];
        foreach ($list as $code) {
            $code = strtoupper(trim($code));
            $cached = $this->getCachedCurrencyPrices($code);
            if ($cached) {
                $result[strtolower($code)] = $cached['data'] ?? $cached;
            }
        }
        return $result;
    }

    /**
     * Get gold prices and cache the response forever
     */
    public function getGoldPrices()
    {
        try {
            $response = Http::get("{$this->baseUrl}/gold", [
                'token' => $this->token
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cacheData = [
                    'data' => $data,
                    'cached_at' => now()->toISOString(),
                    // forever cache: no expires_at
                ];
                Cache::forever('egrates_gold_prices', $cacheData);
                return $data;
            }

            Log::error('Egrates gold prices API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Egrates gold prices API exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get banks data and cache the response forever
     */
    public function getBanks()
    {
        try {
            $response = Http::get("{$this->baseUrl}/banks", [
                'token' => $this->token
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cacheData = [
                    'data' => $data,
                    'cached_at' => now()->toISOString(),
                    // forever cache: no expires_at
                ];
                Cache::forever('egrates_banks', $cacheData);
                return $data;
            }

            Log::error('Egrates banks API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Egrates banks API exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get cached USD prices without auto-refresh
     */
    public function getCachedUsdPrices()
    {
        return Cache::get('egrates_usd_prices');
    }

    public function getCachedGoldPrices()
    {
        return Cache::get('egrates_gold_prices');
    }

    public function getCachedBanks()
    {
        return Cache::get('egrates_banks');
    }
}
