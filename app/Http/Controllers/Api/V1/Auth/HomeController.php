<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\BenefitResource;
use App\Http\Resources\BranchResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ClientReviewResource;
use App\Http\Resources\ContractResource;
use App\Http\Resources\SliderResource;
use App\Services\BenefitService;
use App\Services\BranchService;
use App\Services\CategoryService;
use App\Services\ClientReviewService;
use App\Services\ContractService;
use App\Services\SliderService;
use App\Traits\BaseApiResponseTrait;

/**
 * @group Api
 * @subgroup Home
 */
class HomeController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(CategoryService $categoryService)
    {
        $this->service   = $categoryService;
        $this->relations = [];
        parent::__construct($categoryService, CategoryResource::class);

    }

    /**
     * Home items.
     * param Keyword for search.
     *
     */
    public function index(): mixed
    {
        request()->merge(['page' => false, 'limit' => false, 'active' => true]);
        $sliders        = app(SliderService::class)->search(request()->all(), []);
        $contracts      = app(ContractService::class)->search(request()->all(), []);
        $benefit        = app(BenefitService::class)->search(request()->all(), []);
        $client_reviews = app(ClientReviewService::class)->search(request()->all(), []);
        $branches       = app(BranchService::class)->search(request()->merge(['scope' => 'mini'])->all(), ['region']);

        return $this->respondWithArray([
            'sliders'        => SliderResource::collection($sliders ?? []),
            'contracts'      => ContractResource::collection($contracts ?? []),
            'benefit'        => BenefitResource::collection($benefit ?? []),
            'client_reviews' => ClientReviewResource::collection($client_reviews ?? []),
            'branches'       => BranchResource::collection($branches ?? []),
        ]);

    }

}
