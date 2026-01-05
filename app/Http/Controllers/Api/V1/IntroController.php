<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\IntroResource;
use App\Services\IntroService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup intros
 */
class IntroController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(IntroService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, IntroResource::class);
    }

    /**
     * Intro list.
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
     * Intro show.
     * @urlParam id required The ID of the intro.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}

