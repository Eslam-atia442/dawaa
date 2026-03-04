<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Store\CreateRequest;
use App\Http\Requests\Admin\Store\UpdateRequest;
use App\Models\Store;
use App\Exports\StoreExport;
use App\Jobs\ExportJob;
use App\Services\ExportService;
use App\Services\CountryService;
use App\Services\RegionService;
use App\Services\CityService;
use Exception;
use App\Services\StoreService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class StoreController extends BaseWebController
{
    public object $service;
    public object $countryService;
    public object $regionService;
    public object $cityService;
    public string $table;
    public string $guard;
    public array $relations;
    protected ExportService $exportService;

    public function __construct(
        StoreService $service,
        CountryService $countryService,
        RegionService $regionService,
        CityService $cityService,
        ExportService $exportService,
        $table = 'stores',
        $guard = 'admin'
    )
    {
        $this->service = $service;
        $this->countryService = $countryService;
        $this->regionService = $regionService;
        $this->cityService = $cityService;
        $this->exportService = $exportService;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['city'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'store');
    }

    public function create(): View
    {
        $countries = $this->countryService->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('countries'));
    }

    public function edit($id): View
    {
        $relations = array_merge($this->relations, ['city.region', 'city.region.country']);
        $row = $this->service->find($id, $relations);
        $countries = $this->countryService->search(['limit' => false, 'page' => false, 'active' => true], [], []);

        $regions = collect();
        $cities = collect();
        if ($row->city && $row->city->region) {
            $regions = $this->regionService->search(['country' => $row->city->region->country_id], [], ['limit' => false, 'page' => false]);
            $cities = $this->cityService->search(['region' => $row->city->region_id], [], ['limit' => false, 'page' => false]);
        }

        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'countries', 'regions', 'cities'));
    }

    public function getRegionsByCountry(Request $request): JsonResponse
    {
        $countryId = $request->get('country_id');
        $regions = $this->regionService->search(['country' => $countryId], [], ['limit' => false, 'page' => false]);
        return response()->json($regions->map(fn($r) => ['id' => $r->id, 'name' => $r->name]));
    }

    public function getCitiesByRegion(Request $request): JsonResponse
    {
        $regionId = $request->get('region_id');
        $cities = $this->cityService->search(['region' => $regionId], [], ['limit' => false, 'page' => false]);
        return response()->json($cities->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
    }
    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $this->service->create($request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }
    public function update(UpdateRequest $request, Store $store): JsonResponse
    {
        try {
            $this->service->update($store, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }

    public function toggleField(Request $request, $store, $key)
    {
        return $this->service->toggleField($store, $key);
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'data' => 'required|json'
        ]);

        return $this->destroy($request->input('data'));
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $filters = collect($request->except(['_token']))
                ->filter(function ($value) {
                    if (is_array($value)) {
                        return !empty(array_filter($value, fn($v) => $v !== '' && $v !== null));
                    }
                    return $value !== '' && $value !== null;
                })
                ->map(function ($value) {
                    if (is_array($value)) {
                        return array_filter($value, fn($v) => $v !== '' && $v !== null);
                    }
                    return $value;
                })
                ->toArray();

           

            $export = $this->exportService->createExport(
                name: __('trans.store.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'Store',
                parameters: $filters
            );

            ExportJob::dispatch(
                export: $export,
                exportClass: StoreExport::class,
                filters: $filters
            );

            return response()->json([
                'success' => true,
                'message' => __('trans.export_queued'),
                'export_id' => $export->id
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
