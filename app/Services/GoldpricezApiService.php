<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldpricezApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct(){
        $this->apiKey  = config('services.goldpricez.api_key');
        $this->baseUrl = config('services.goldpricez.base_url');
    }

    /**
     * Get gold rates for a specific currency and unit type
     *
     * @param string $currencyCode Currency code (e.g., USD, EUR, CAD, SAR, AED, etc.)
     * @param string $unitType Unit type (e.g., gram, ounce, kg, tola-india, tola-pakistan, etc.)
     * @return array|null
     */
    public function getGoldRates(string $currencyCode, string $unitType): ?array{
        try {
            $currencyCode = strtolower($currencyCode);
            $unitType     = strtolower($unitType);
            $url          = "{$this->baseUrl}/currency/{$currencyCode}/measure/{$unitType}";
            $url          = strtolower($url);

            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->get($url);

            if ($response->successful()) {
                $result = $response->json();

                // Handle double JSON encoding if present
                if (is_string($result)) {
                    $result = json_decode($result, true);
                }

                return $result;
            }

            Log::error('Goldpricez API failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
                'url'      => $url,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Goldpricez API exception', [
                'message'  => $e->getMessage(),
                'currency' => $currencyCode,
                'unit'     => $unitType,
            ]);
            return null;
        }
    }

    /**
     * Extract current gold price from the result
     *
     * @param array $result API response result
     * @param string $currencyCode Currency code
     * @param string $unitType Unit type
     * @return float|null
     */
    public function getCurrentPrice(array $result, string $currencyCode, string $unitType): ?float{
        $key = strtolower($unitType) . '_in_' . strtolower($currencyCode);
        return $result[$key] ?? null;
    }

    /**
     * Get gold price update timestamp
     *
     * @param array $result API response result
     * @param string $currencyCode Currency code
     * @return string|null
     */
    public function getGoldUpdateTimestamp(array $result, string $currencyCode): ?string{
        $key = 'gmt_ounce_price_' . strtolower($currencyCode) . '_updated';
        return $result[$key] ?? $result['gmt_ounce_price_usd_updated'] ?? null;
    }

    /**
     * Get currency rate (1 USD to target currency)
     *
     * @param array $result API response result
     * @param string $currencyCode Currency code
     * @return float|null
     */
    public function getCurrencyRate(array $result, string $currencyCode): ?float{
        if (strtolower($currencyCode) === 'usd') {
            return 1.0;
        }
        $key = 'usd_to_' . strtolower($currencyCode);
        return $result[$key] ?? null;
    }

    /**
     * Get currency update timestamp
     *
     * @param array $result API response result
     * @param string $currencyCode Currency code
     * @return string|null
     */
    public function getCurrencyUpdateTimestamp(array $result, string $currencyCode): ?string{
        if (strtolower($currencyCode) === 'usd') {
            return $this->getGoldUpdateTimestamp($result, $currencyCode);
        }
        $key = 'gmt_' . strtolower($currencyCode) . '_updated';
        return $result[$key] ?? null;
    }

    /**
     * Get silver rates for a specific currency and unit type
     *
     * @param string $currencyCode Currency code (e.g., USD, EUR, CAD, SAR, AED, etc.)
     * @param string $unitType Unit type (e.g., gram, ounce, kg, etc.)
     * @return array|null
     */
    public function getSilverRates(string $currencyCode, string $unitType): ?array{
        try {
            $currencyCode = strtolower($currencyCode);
            $unitType     = strtolower($unitType);
            // Try silver endpoint - if API doesn't support it, we'll handle the error
            $url = "{$this->baseUrl}/silver/currency/{$currencyCode}/measure/{$unitType}";
            $url = strtolower($url);

            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->get($url);

            if ($response->successful()) {
                $result = $response->json();

                // Handle double JSON encoding if present
                if (is_string($result)) {
                    $result = json_decode($result, true);
                }

                return $result;
            }

            // If silver endpoint doesn't exist, try alternative endpoint
            $url      = str_replace('/silver/', '/', $url);
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->get($url . '?metal=silver');

            if ($response->successful()) {
                $result = $response->json();
                if (is_string($result)) {
                    $result = json_decode($result, true);
                }
                return $result;
            }

            Log::error('Goldpricez Silver API failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
                'url'      => $url,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Goldpricez Silver API exception', [
                'message'  => $e->getMessage(),
                'currency' => $currencyCode,
                'unit'     => $unitType,
            ]);
            return null;
        }
    }


    /**
     * Calculate karat rates from base price
     *
     * @param float $basePrice Base price (24 karat)
     * @return array
     */
    public function calculateKaratRates(float $basePrice): array{

        return [
            '24karat' => round($basePrice, 2),
            '22karat' => round($basePrice * 0.916, 2),  // 22/24 = 0.916
            '21karat' => round($basePrice * 0.875, 2),  // 21/24 = 0.875
            '18karat' => round($basePrice * 0.750, 2),  // 18/24 = 0.750
            '16karat' => round($basePrice * 0.666, 2),  // 16/24 = 0.666
            '14karat' => round($basePrice * 0.5833, 2), // 14/24 = 0.5833
        ];
    }
}

