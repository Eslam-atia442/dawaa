<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\SettingResource;
use App\Services\SettingService;
use App\Traits\BaseApiResponseTrait;
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


}
