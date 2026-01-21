<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\ChildProductResource;
use App\Services\ChildProductService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup child-products
 */
class ChildProductController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(ChildProductService $service)
    {
        $this->service   = $service;
        $this->relations = ['parent', 'parent.store', 'parent.category', 'parent.brand', 'parent.city'];
        parent::__construct($service, ChildProductResource::class);
    }

    /**
     * Child Product list.
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
     * Child Product show.
     * @urlParam id required The ID of the child product.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}