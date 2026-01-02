<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Setting\CreateRequest;
use App\Http\Requests\Admin\Setting\UpdateRequest;
use App\Models\Setting;
use Exception;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Services\EgratesApiService;


class SettingController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        SettingService $service,
                       $table = 'settings',
                       $guard = 'admin'
    )
    {
        $this->service   = $service;
        $this->table     = $table;
        $this->guard     = $guard;
        $this->relations = [];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations);
    }

    public function index(): View|JsonResponse
    {
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index');
    }

    public function update(UpdateRequest $request, Setting $setting): JsonResponse
    {
        Cache::forget('globalSetting');
        try {
            $this->service->updateSettings($request->validated());
            return response()->json(['url' => route('admin.home')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cacheEgratesGold(EgratesApiService $egratesService): JsonResponse
    {
        try {
            $egratesService->getGoldPrices();
            return response()->json([
                'status' => 'success',
                'message' => trans('trans.egrates.gold_updated')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cacheEgratesCurrencies(Request $request, EgratesApiService $egratesService): JsonResponse
    {
        try {
            $codes = $request->input('codes');
            if (empty($codes)) {
                $codes = (array) config('services.egrates.currencies', ['USD','EUR','GBP','AED']);
            } elseif (is_string($codes)) {
                // Support comma separated string
                $codes = array_filter(array_map('trim', explode(',', $codes)));
            }

            foreach ($codes as $code) {
                $egratesService->getCurrencyPrices($code);
            }

            return response()->json([
                'status' => 'success',
                'message' => trans('trans.egrates.currencies_updated')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
