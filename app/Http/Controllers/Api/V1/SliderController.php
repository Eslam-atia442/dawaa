<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\SliderResource;
use App\Services\SliderService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup sliders
 */
class SliderController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(SliderService $service)
    {
        $this->service   = $service;
        $this->relations = [];
        parent::__construct($service, SliderResource::class);
    }

    /**
     * Slider list.
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
     * Slider show.
     * @urlParam id required The ID of the slider.
     */
    public function show($id): mixed
    {
        $model = $this->service->find($id, $this->relations);
        return $this->respondWithModel($model);
    }
}

