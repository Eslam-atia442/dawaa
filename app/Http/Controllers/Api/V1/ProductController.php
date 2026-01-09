<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup products
 */
class ProductController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(ProductService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, ProductResource::class);
    }

    /**
     * Product list.
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
     * Product show.
     * @urlParam id required The ID of the product.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}

