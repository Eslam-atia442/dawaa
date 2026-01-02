<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Services\EgratesApiService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @group Egrates API
 *
 * Egrates API endpoints for retrieving cached currency, gold, and banking data.
 *
 * These endpoints provide access to cached data from the Egrates external API with comprehensive cache metadata.
 * All data is automatically cached for 1 hour and includes timestamps for cache management.
 *
 * @authenticated false
 */
class EgratesController extends BaseApiController
{
    use BaseApiResponseTrait;
    protected $egratesService;

    public function __construct(EgratesApiService $egratesService)
    {
        $this->egratesService = $egratesService;
    }

    /**
     * Get cached USD prices with cache update timestamp
     *
     * @group Egrates API
     * @groupName Egrates Data
     *
     * Retrieves cached USD prices data from the Egrates API with detailed cache metadata.
     * If no cached data exists, fresh data will be fetched automatically.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "USD prices retrieved successfully",
     *   "data": {
     *     "data": {
     *       "prices": [
     *         {
     *           "currency": "USD",
     *           "buy_rate": 3.75,
     *           "sell_rate": 3.78,
     *           "timestamp": "2024-01-15T10:30:00Z"
     *         }
     *       ]
     *     },
     *     "cache_info": {
     *       "cached_at": "2024-01-15T10:30:00.000000Z",
     *       "expires_at": "2024-01-15T11:30:00.000000Z",
     *       "cache_key": "egrates_usd_prices",
     *       "is_cached": true,
     *       "ttl_seconds": 3600
     *     }
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to fetch USD prices data",
     *   "data": null
     * }
     */
    public function getUsdPrices(Request $request)
    {
        try {
            $cacheKey   = 'egrates_usd_prices';
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return $this->errorResponse('USD prices not cached yet', 404);
            }

             $data      = $cachedData['data'] ?? $cachedData;
            $cachedAt  = $cachedData['cached_at'] ?? now()->toISOString();
            $expiresAt = $cachedData['expires_at'] ?? now()->addHours(1)->toISOString();

            return $this->respondWithArray([
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving USD prices: ' . $e->getMessage(), 500);
        }
    }

    public function getEurPrices(Request $request)
    {
        try {
            $cacheKey   = 'egrates_eur_prices';
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return $this->errorResponse('EUR prices not cached yet', 404);
            }

            $data = $cachedData['data'] ?? $cachedData;

            return $this->respondWithArray([
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving EUR prices: ' . $e->getMessage(), 500);
        }
    }

    public function getGbpPrices(Request $request)
    {
        try {
            $cacheKey   = 'egrates_gbp_prices';
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return $this->errorResponse('GBP prices not cached yet', 404);
            }

            $data = $cachedData['data'] ?? $cachedData;

            return $this->respondWithArray([
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving GBP prices: ' . $e->getMessage(), 500);
        }
    }

    public function getAedPrices(Request $request)
    {
        try {
            $cacheKey   = 'egrates_aed_prices';
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return $this->errorResponse('AED prices not cached yet', 404);
            }

            $data = $cachedData['data'] ?? $cachedData;

            return $this->respondWithArray([
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving AED prices: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get cached gold prices with cache update timestamp
     *
     * @group Egrates API
     *
     * Retrieves cached gold prices data from the Egrates API with detailed cache metadata.
     * If no cached data exists, fresh data will be fetched automatically.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Gold prices retrieved successfully",
     *   "data": {
     *     "data": {
     *       "gold": [
     *         {
     *           "type": "24K",
     *           "buy_rate": 250.50,
     *           "sell_rate": 252.75,
     *           "unit": "per gram",
     *           "timestamp": "2024-01-15T10:30:00Z"
     *         }
     *       ]
     *     },
     *     "cache_info": {
     *       "cached_at": "2024-01-15T10:30:00.000000Z",
     *       "expires_at": "2024-01-15T11:30:00.000000Z",
     *       "cache_key": "egrates_gold_prices",
     *       "is_cached": true,
     *       "ttl_seconds": 3600
     *     }
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to fetch gold prices data",
     *   "data": null
     * }
     */
    public function getGoldPrices(Request $request)
    {
        try {
            $cacheKey   = 'egrates_gold_prices';
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return $this->errorResponse('Gold prices not cached yet', 404);
            }

            // Extract data and metadata from cache
            $data      = $cachedData['data'] ?? $cachedData;
            $cachedAt  = $cachedData['cached_at'] ?? now()->toISOString();
            $expiresAt = $cachedData['expires_at'] ?? now()->addHours(1)->toISOString();

            return $this->respondWithArray([
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving gold prices: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get cached banks data with cache update timestamp
     *
     * @group Egrates API
     *
     * Retrieves cached banks data from the Egrates API with detailed cache metadata.
     * If no cached data exists, fresh data will be fetched automatically.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Banks data retrieved successfully",
     *   "data": {
     *     "data": {
     *       "banks": [
     *         {
     *           "id": 1,
     *           "name": "National Bank of Egypt",
     *           "code": "NBE",
     *           "status": "active",
     *           "rates": {
     *             "buy": 3.75,
     *             "sell": 3.78
     *           }
     *         }
     *       ]
     *     },
     *     "cache_info": {
     *       "cached_at": "2024-01-15T10:30:00.000000Z",
     *       "expires_at": "2024-01-15T11:30:00.000000Z",
     *       "cache_key": "egrates_banks",
     *       "is_cached": true,
     *       "ttl_seconds": 3600
     *     }
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to fetch banks data",
     *   "data": null
     * }
     */
    public function getBanks(Request $request)
    {
        try {
            $cacheKey   = 'egrates_banks';
            $cachedData = Cache::get($cacheKey);
            if (!$cachedData) {
                return $this->errorResponse('Banks data not cached yet', 404);
            }

            // Extract data and metadata from cache
            $data      = $cachedData['data'] ?? $cachedData;
            $cachedAt  = $cachedData['cached_at'] ?? now()->toISOString();
            $expiresAt = $cachedData['expires_at'] ?? now()->addHours(1)->toISOString();

            return $this->respondWithArray([
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get all cached Egrates data with timestamps
     *
     * @group Egrates API
     *
     * Retrieves all cached Egrates data (USD prices, gold prices, and banks) with detailed cache metadata.
     * This endpoint is useful for getting all data in a single request for dashboards or bulk operations.
     * If any cached data doesn't exist, fresh data will be fetched automatically.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "All Egrates data retrieved successfully",
     *   "data": {
     *     "usd_prices": {
     *       "data": {
     *         "prices": [
     *           {
     *             "currency": "USD",
     *             "buy_rate": 3.75,
     *             "sell_rate": 3.78
     *           }
     *         ]
     *       },
     *       "cache_info": {
     *         "cached_at": "2024-01-15T10:30:00.000000Z",
     *         "expires_at": "2024-01-15T11:30:00.000000Z",
     *         "cache_key": "egrates_usd_prices",
     *         "is_cached": true,
     *         "ttl_seconds": 3600
     *       }
     *     },
     *     "gold_prices": {
     *       "data": {
     *         "gold": [
     *           {
     *             "type": "24K",
     *             "buy_rate": 250.50,
     *             "sell_rate": 252.75
     *           }
     *         ]
     *       },
     *       "cache_info": {
     *         "cached_at": "2024-01-15T10:30:00.000000Z",
     *         "expires_at": "2024-01-15T11:30:00.000000Z",
     *         "cache_key": "egrates_gold_prices",
     *         "is_cached": true,
     *         "ttl_seconds": 3600
     *       }
     *     },
     *     "banks": {
     *       "data": {
     *         "banks": [
     *           {
     *             "id": 1,
     *             "name": "National Bank of Egypt",
     *             "code": "NBE"
     *           }
     *         ]
     *       },
     *       "cache_info": {
     *         "cached_at": "2024-01-15T10:30:00.000000Z",
     *         "expires_at": "2024-01-15T11:30:00.000000Z",
     *         "cache_key": "egrates_banks",
     *         "is_cached": true,
     *         "ttl_seconds": 3600
     *       }
     *     }
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error retrieving Egrates data: Network timeout",
     *   "data": null
     * }
     */
    public function getAllData(Request $request)
    {
        try {
            $result    = [];
            $cacheKeys = [
                'usd_prices'  => 'egrates_usd_prices',
                'eur_prices'  => 'egrates_eur_prices',
                'gbp_prices'  => 'egrates_gbp_prices',
                'aed_prices'  => 'egrates_aed_prices',
                'gold_prices' => 'egrates_gold_prices',
                'banks'       => 'egrates_banks'
            ];

            foreach ($cacheKeys as $key => $cacheKey) {
                $cachedData = Cache::get($cacheKey);
                if (!$cachedData) {
                    continue;
                }
                $result[$key] = $cachedData['data'] ?? $cachedData;
            }

            return $this->respondWithArray([
                'data' => $result,
            ]);


        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving Egrates data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Force refresh cache for all or specific data type
     *
     * @group Egrates API
     *
     * Forces a refresh of the cache for all or specific data types.
     * This endpoint is useful when you need fresh data immediately without waiting for cache expiration.
     *
     * @queryParam type string The type of cache to refresh. Options: all, usd, gold, banks. Default: all
     *
     * @response 200 {
     *   "success": true,
     *   "message": "All caches refreshed successfully",
     *   "data": []
     * }
     *
     * @response 200 {
     *   "success": true,
     *   "message": "USD prices cache refreshed successfully",
     *   "data": []
     * }
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Gold prices cache refreshed successfully",
     *   "data": []
     * }
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Banks data cache refreshed successfully",
     *   "data": []
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error refreshing cache: Network timeout",
     *   "data": null
     * }
     */
    public function refreshCache(Request $request)
    {
        try {
            $type = $request->get('type', 'all'); // all, usd, gold, banks

            switch ($type) {
                case 'usd':
                    $data    = $this->egratesService->getUsdPrices();
                    $message = 'USD prices cache refreshed successfully';
                    break;
                case 'eur':
                    $data    = $this->egratesService->getEurPrices();
                    $message = 'EUR prices cache refreshed successfully';
                    break;
                case 'gbp':
                    $data    = $this->egratesService->getGbpPrices();
                    $message = 'GBP prices cache refreshed successfully';
                    break;
                case 'aed':
                    $data    = $this->egratesService->getAedPrices();
                    $message = 'AED prices cache refreshed successfully';
                    break;
                case 'gold':
                    $data    = $this->egratesService->getGoldPrices();
                    $message = 'Gold prices cache refreshed successfully';
                    break;
                case 'banks':
                    $data    = $this->egratesService->getBanks();
                    $message = 'Banks data cache refreshed successfully';
                    break;
                case 'all':
                default:
                    $this->egratesService->getUsdPrices();
                    $this->egratesService->getEurPrices();
                    $this->egratesService->getGbpPrices();
                    $this->egratesService->getAedPrices();
                    $this->egratesService->getGoldPrices();
                    $this->egratesService->getBanks();
                    $message = 'All caches refreshed successfully';
                    break;
            }

            return $this->respondWithSuccess( $message);

        } catch (\Exception $e) {
            return $this->errorResponse('Error refreshing cache: ' . $e->getMessage(), 500);
        }
    }
}
