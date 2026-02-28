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
     * @queryParam parent_id Filter by parent product ID.
     * param Keyword for search.
     *
     */
    public function index(): mixed
    {
        $filters = request()->all();
        $filters = array_merge($filters, ['page' => false, 'limit' => false, 'active' => true, 'expiryDate' => now()]);

        // If parent_id is provided in query params, filter by it
        if (request()->has('parent_id')) {
            $filters['parent'] = request('parent_id');
        }

        $models = $this->service->search($filters, $this->relations);
        return $this->respondWithCollection($models);
    }

    /**
     * Get child products by parent product ID.
     * @urlParam product required The ID of the parent product.
     *
     */
    public function getByProduct($productId): mixed
    {
        request()->merge(['page' => false, 'limit' => false, 'active' => true, 'parent' => $productId, 'expiryDate' => now()]);
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