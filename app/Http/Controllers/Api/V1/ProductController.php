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
        $this->relations = ['store', 'category', 'brand'];
        parent::__construct($service, ProductResource::class);
    }

    /**
     * Product list.
     * @queryParam keyword Filter by keyword.
     * @queryParam parent Filter by parent ID.
     * @queryParam store Filter by store ID.
     * @queryParam city Filter by city ID.
     * @queryParam category Filter by category ID.
     * @queryParam brand Filter by brand ID.
     * @queryParam fromPrice Filter by from price.
     * @queryParam toPrice Filter by to price.
     * @queryParam hasDiscount Filter by has discount.
     * @queryParam recentlyAdded Filter by recently added products.
     * @queryParam hasChildren Filter by has children products.
     */
    public function index(): mixed
    {
        request()->merge([ 'active' => true, 'parent' => true, 'hasChildren' => true]);
        $models = $this->service->search(request()->all(), ['store', 'category', 'brand' ,'oldestChildProduct']);
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

