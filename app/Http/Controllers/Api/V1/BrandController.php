<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\BrandResource;
use App\Services\BrandService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup brands
 */
class BrandController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(BrandService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, BrandResource::class);
    }

    /**
     * Brand list.
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
     * Brand show.
     * @urlParam id required The ID of the brand.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}

