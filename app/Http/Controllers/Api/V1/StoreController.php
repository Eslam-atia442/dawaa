<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\StoreResource;
use App\Services\StoreService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup stores
 */
class StoreController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(StoreService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, StoreResource::class);
    }

    /**
     * Store list.
     * param Keyword for search.
     *
     */
    public function index(): mixed
    {
        request()->merge(['page' => false, 'limit' => false, 'active' => true]);
        $models = $this->service->search(request()->all(), $this->relations);
        return $this->respondWithCollection($models);
    }

    /**
     * Store show.
     * @urlParam id required The ID of the store.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}

