<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\ContactUsRequest;
use App\Http\Resources\ContactUsResource;
use App\Services\ContactUsService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup contactuses
 */
class ContactUsController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(ContactUsService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, ContactUsResource::class);
    }

    /**
     * ContactUs list.
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
     * ContactUs show.
     * @urlParam id required The ID of the contactUs.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
    public function store(ContactUsRequest $request): mixed
    {
        
        $model = $this->service->create($request->validated());
        return $this->respondWithModel($model);
    }
}

