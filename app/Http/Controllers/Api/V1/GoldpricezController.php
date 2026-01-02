<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Controllers\Controller;
use App\Services\GoldpricezApiService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

/**
 * @group Api
 * @subgroup Gold
 */
class GoldpricezController extends Controller
{
    use BaseApiResponseTrait;

    protected GoldpricezApiService $goldpricezService;

    public function __construct(GoldpricezApiService $goldpricezService){

        $this->goldpricezService = $goldpricezService;
    }

    /**
     * Get gold rates for a specific currency and unit type
     *
     *
     */
    public function getGoldRates(Request $request): JsonResponse{
        try {

            $currencyCode = $request->get('currency', 'EGP');
            $unitType     = $request->get('unit', 'gram');

            if (empty($currencyCode)) {
                return $this->respondWithError('Currency code is required', 400);
            }

            if (empty($unitType)) {
                return $this->respondWithError('Unit type is required', 400);
            }

            // Check cache first (normalize to match command format)
            $currencyUpper = strtoupper($currencyCode);
            $unitLower = strtolower($unitType);
            $cacheKey = "goldpricez_gold_{$currencyUpper}_{$unitLower}";
            $cachedData = Cache::get($cacheKey);

            if ($cachedData) {
                // Return cached data
                return $this->respondWithArray([
                    'status' => 200,
                    'data'   => $cachedData,
                ]);
            }

            // Cache doesn't exist, fetch from API and cache it
            $result = $this->goldpricezService->getGoldRates($currencyCode, $unitType);

            if (!$result) {
                // Try to run the command to refresh cache
                try {
                    Artisan::call('goldpricez:cache-prices', [
                        'currency' => strtoupper($currencyCode),
                        'unit' => strtolower($unitType),
                    ]);
                } catch (\Exception $e) {
                    // Command execution failed, continue with error response
                }

                // Check cache again after command execution
                $cachedData = Cache::get($cacheKey);
                if ($cachedData) {
                    return $this->respondWithArray([
                        'status' => 200,
                        'data'   => $cachedData,
                    ]);
                }

                return $this->respondWithError('Failed to fetch gold rates from API', 500);
            }

            // Prepare response data
            $currentPrice            = $this->goldpricezService->getCurrentPrice($result, $currencyCode, $unitType);
            $goldUpdateTimestamp     = $this->goldpricezService->getGoldUpdateTimestamp($result, $currencyCode);
            $currencyRate            = $this->goldpricezService->getCurrencyRate($result, $currencyCode);
            $currencyUpdateTimestamp = $this->goldpricezService->getCurrencyUpdateTimestamp($result, $currencyCode);

            $karatRates = null;
            if ($currentPrice !== null) {
                $karatRates = $this->goldpricezService->calculateKaratRates($currentPrice);
            }

            $responseData = [
                'currency_code'             => strtoupper($currencyCode),
                'unit_type'                 => strtolower($unitType),
                'metal_type'                => 'gold',
                'current_price'             => $currentPrice,
                'gold_update_timestamp'     => $goldUpdateTimestamp,
                'currency_rate'             => $currencyRate,
                'currency_update_timestamp' => $currencyUpdateTimestamp,
                'karat_rates'               => $karatRates,
                'raw_data'                  => $result,
                'cached_at'                 => now()->toISOString(),
            ];

            // Cache the data forever
            Cache::forever($cacheKey, $responseData);

            return $this->respondWithArray([
                'status' => 200,
                'data'   => $responseData,
            ]);

        } catch (\Exception $e) {
            return $this->respondWithError('Error retrieving gold rates: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Get silver rates for a specific currency and unit type
     *
     *
     */
    public function getSilverRates(Request $request): JsonResponse
    {
        try {
            $currencyCode = $request->get('currency', 'EGP');
            $unitType = $request->get('unit', 'gram');

            if (empty($currencyCode)) {
                return $this->respondWithError('Currency code is required', 400);
            }

            if (empty($unitType)) {
                return $this->respondWithError('Unit type is required', 400);
            }

            // Check cache first (normalize to match command format)
            $currencyUpper = strtoupper($currencyCode);
            $unitLower = strtolower($unitType);
            $cacheKey = "goldpricez_silver_{$currencyUpper}_{$unitLower}";

            $cachedData = Cache::get($cacheKey);
 
            if ($cachedData) {
                // Return cached data
                return $this->respondWithArray([
                    'status' => 200,
                    'data' => $cachedData,
                ]);
            }

            // Cache doesn't exist, fetch from API and cache it
            $result = $this->goldpricezService->getSilverRates($currencyCode, $unitType);

            if (!$result) {
                // Try to run the command to refresh cache
                try {
                    Artisan::call('goldpricez:cache-prices', [
                        'currency' => strtoupper($currencyCode),
                        'unit' => strtolower($unitType),
                    ]);
                } catch (\Exception $e) {
                    // Command execution failed, continue with error response
                }

                // Check cache again after command execution
                $cachedData = Cache::get($cacheKey);
                if ($cachedData) {
                    return $this->respondWithArray([
                        'status' => 200,
                        'data' => $cachedData,
                    ]);
                }

                return $this->respondWithError('Failed to fetch silver rates from API', 500);
            }

            // Prepare response data
            $currentPrice = $this->goldpricezService->getCurrentPrice($result, $currencyCode, $unitType);

            $responseData = [
                'currency_code' => strtoupper($currencyCode),
                'unit_type' => strtolower($unitType),
                'metal_type' => 'silver',
                'current_price' => $currentPrice,
                'raw_data' => $result,
                'cached_at' => now()->toISOString(),
            ];

            // Cache the data forever
            Cache::forever($cacheKey, $responseData);

            return $this->respondWithArray([
                'status' => 200,
                'data' => $responseData,
            ]);

        } catch (\Exception $e) {
            return $this->respondWithError('Error retrieving silver rates: ' . $e->getMessage(), 500);
        }
    }

}

