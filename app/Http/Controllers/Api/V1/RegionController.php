<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\RegionResource;
use App\Services\RegionService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup Regions
 */
class RegionController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(RegionService $regionService)
    {
        $this->service   = $regionService;
        $this->relations = ['country'];
        parent::__construct($regionService, RegionResource::class);
    }

    /**
     * Region list.
     * @queryParam country_id Filter by country ID.
     * param Keyword for search.
     *
     */
    public function index(): mixed
    {
        $filters = request()->all();
        $filters = array_merge($filters, ['page' => false, 'limit' => false, 'active' => true]);

        // If country_id is provided in query params, filter by it
        if (request()->has('country_id')) {
            $filters['country'] = request('country_id');
        }

        $models = $this->service->search($filters, $this->relations);
        return $this->respondWithCollection($models);
    }

    /**
     * Get regions by country ID.
     * @urlParam country required The ID of the country.
     *
     */
    public function getByCountry($countryId): mixed
    {
        request()->merge(['page' => false, 'limit' => false, 'active' => true, 'country' => $countryId]);
        $models = $this->service->search(request()->all(), $this->relations);
        return $this->respondWithCollection($models);
    }
}