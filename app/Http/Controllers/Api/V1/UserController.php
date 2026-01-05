<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup users
 */
class UserController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(UserService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, UserResource::class);
    }

    /**
     * User list.
     * param Keyword for search.
     *
     */
    public function index(): mixed
    {
        $models = $this->service->search(request()->all(), $this->relations);
        return $this->respondWithCollection($models);
    }

    /**
     * User show.
     * @urlParam id required The ID of the user.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}

