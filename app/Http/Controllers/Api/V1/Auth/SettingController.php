<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\SettingResource;
use App\Services\SettingService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * @group Api
 * @subgroup settings
 */
class SettingController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(SettingService $countryService)
    {
        $this->service   = $countryService;
        $this->relations = [];
        parent::__construct($countryService, SettingResource::class);

    }

    /**
     * Setting list.
     * @queryParam key string required The key of the setting.
     *
     */
    public function index(): mixed{
        $models = Cache::get('globalSetting');
        if (request()->has('key')) {
            $models = $models->where('key', request()->key);
        }
        return $this->respondWithCollection($models);
    }

    /**
     * App review check.
     * @queryParam version string required The current app version.
     */
    public function appReview(): JsonResponse
    {
        $version = request()->query('version');
        $inReviewVersion = globalSetting('in_review_version');
        $isReview = (bool) globalSetting('is_review');
        $forceUpdate = (bool) globalSetting('force_update');

        $isInReview = $isReview && $version && $inReviewVersion && $version === $inReviewVersion;

        return response()->json([
            'status' => 200,
            'data' => [
                'in_review_version' => $inReviewVersion,
                'is_review' => $isReview,
                'is_in_review' => $isInReview,
                'force_update' => $forceUpdate,
            ],
        ]);
    }
}
