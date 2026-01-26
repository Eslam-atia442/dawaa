<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\CityResource;
use App\Services\CityService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup Cities
 */
class CityController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(CityService $cityService)
    {
        $this->service   = $cityService;
        $this->relations = ['region'];
        parent::__construct($cityService, CityResource::class);
    }

    /**
     * City list.
     * @queryParam region_id Filter by region ID.
     * param Keyword for search.
     *
     */
    public function index(): mixed
    {
        $filters = request()->all();
        $filters = array_merge($filters, ['page' => false, 'limit' => false]);

        // If region_id is provided in query params, filter by it
        if (request()->has('region_id')) {
            $filters['region'] = request('region_id');
        }

        $models = $this->service->search($filters, $this->relations);
        return $this->respondWithCollection($models);
    }

    /**
     * Get cities by region ID.
     * @urlParam region required The ID of the region.
     *
     */
    public function getByRegion($regionId): mixed
    {
        request()->merge(['page' => false, 'limit' => false, 'region' => $regionId]);
        $models = $this->service->search(request()->all(), $this->relations);
        return $this->respondWithCollection($models);
    }
}