<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\CountryResource;
use App\Services\CountryService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup Countries
 */
class CountryController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(CountryService $countryService)
    {
        $this->service   = $countryService;
        $this->relations = [];
        parent::__construct($countryService, CountryResource::class);

    }

    /**
     * Country list.
     * param Keyword for search.
     *
     */
    public function index(): mixed
    {
        request()->merge(['page' => false, 'limit' => false , 'active' => true]);
        $models = $this->service->search(request()->all(), $this->relations);
        return $this->respondWithCollection($models);
    }

}
