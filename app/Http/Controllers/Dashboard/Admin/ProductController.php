<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Product\CreateRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Models\Product;
use App\Exports\ProductExport;
use App\Jobs\ExportJob;
use App\Services\ExportService;
use App\Services\CountryService;
use App\Services\RegionService;
use App\Services\CityService;
use Exception;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductController extends BaseWebController
{
    public object           $service;
    public object           $countryService;
    public object           $regionService;
    public object           $cityService;
    public string           $table;
    public string           $guard;
    public array            $relations;
    protected ExportService $exportService;

    public function __construct(
        ProductService $service,
        CountryService $countryService,
        RegionService  $regionService,
        CityService    $cityService,
        ExportService  $exportService,
                       $table = 'products',
                       $guard = 'admin'
    )
    {
        $this->service        = $service;
        $this->countryService = $countryService;
        $this->regionService  = $regionService;
        $this->cityService    = $cityService;
        $this->exportService  = $exportService;
        $this->table          = $table;
        $this->guard          = $guard;
        $this->relations      = ['store', 'city', 'category', 'brand', 'childProducts', 'parent'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'product');
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $filters = array_merge(request()->all(), ['parent' => true]);
            $rows    = $this->service->search($filters, $this->relations);
            $html    = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }
        $stores     = app(\App\Services\StoreService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $categories = app(\App\Services\CategoryService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $brands     = app(\App\Services\BrandService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $countries  = $this->countryService->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index', compact('stores', 'categories', 'brands', 'countries'));
    }

    public function create(): View
    {
        $stores     = app(\App\Services\StoreService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $categories = app(\App\Services\CategoryService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $brands     = app(\App\Services\BrandService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $countries  = $this->countryService->search(['limit' => false, 'page' => false, 'active' => true], [], []);

        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('stores', 'categories', 'brands', 'countries'));
    }

    public function edit($id): View
    {
        $relations = array_merge($this->relations, ['city.region', 'city.region.country']);
        $row       = $this->service->find($id, $relations);
        // Get stores, categories, brands for dropdowns
        $stores     = app(\App\Services\StoreService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $categories = app(\App\Services\CategoryService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $brands     = app(\App\Services\BrandService::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        $countries  = $this->countryService->search(['limit' => false, 'page' => false, 'active' => true], [], []);

        // Get regions and cities based on selected city if exists
        $regions = collect();
        $cities  = collect();
        if ($row->city && $row->city->region) {
            $regions = $this->regionService->search(['country' => $row->city->region->country_id], [], ['limit' => false, 'page' => false]);
            $cities  = $this->cityService->search(['region' => $row->city->region_id], [], ['limit' => false, 'page' => false]);
        }

        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'stores', 'categories', 'brands', 'countries', 'regions', 'cities'));
    }

    public function getRegionsByCountry(Request $request): JsonResponse
    {

        $countryId = $request->get('country_id');
        $regions   = $this->regionService->search(['country' => $countryId], [], ['limit' => false, 'page' => false]);

        return response()->json($regions->map(function ($region) {
            return ['id' => $region->id, 'name' => $region->name];
        }));
    }

    public function getCitiesByRegion(Request $request): JsonResponse
    {
        $regionId = $request->get('region_id');
        $cities   = $this->cityService->search(['region' => $regionId], [], ['limit' => false, 'page' => false]);
        return response()->json($cities->map(function ($city) {
            return ['id' => $city->id, 'name' => $city->name];
        }));
    }

    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            // Ensure parent_id is null for parent products
            $data['parent_id'] = null;
            $this->service->create($data);
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateRequest $request, Product $product): JsonResponse
    {
        try {
            $data = $request->validated();
            // Ensure parent_id remains null for parent products
            $data['parent_id'] = null;
            $this->service->update($product, $data);
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function toggleField(Request $request, $product, $key)
    {
        return $this->service->toggleField($product, $key);
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

            // Add filter for parent products only
            $filters['parentId'] = null;

            $export = $this->exportService->createExport(
                name      : __('trans.product.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model     : 'Product',
                parameters: $filters
            );

            ExportJob::dispatch(
                export     : $export,
                exportClass: ProductExport::class,
                filters    : $filters
            );

            return response()->json([
                                        'success'   => true,
                                        'message'   => __('trans.export_queued'),
                                        'export_id' => $export->id
                                    ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
