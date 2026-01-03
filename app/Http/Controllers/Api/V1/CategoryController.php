<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup categories
 */
class CategoryController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(CategoryService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, CategoryResource::class);
    }

    /**
     * Category list.
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
     * Category show.
     * @urlParam id required The ID of the category.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}

