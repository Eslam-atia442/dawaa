<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\ChildProduct\CreateRequest;
use App\Http\Requests\Admin\ChildProduct\UpdateRequest;
use App\Models\Product;
use App\Exports\ChildProductExport;
use App\Jobs\ExportJob;
use App\Services\ExportService;
use Exception;
use App\Services\ChildProductService;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ChildProductController extends BaseWebController
{
    public object $service;
    public object $productService;
    public string $table;
    public string $guard;
    public array $relations;
    protected ExportService $exportService;

    public function __construct(
        ChildProductService $service,
        ProductService $productService,
        ExportService $exportService,
        $table = 'child-products',
        $guard = 'admin'
    ) {
        $this->service = $service;
        $this->productService = $productService;
        $this->exportService = $exportService;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['parent', 'parent.store', 'parent.city', 'parent.category', 'parent.brand'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'child-product');
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            // Only show child products (where parent_id is not null)
            $filters = array_merge(request()->all(), ['parentId' => 'not_null']);
            $rows = $this->service->search($filters, $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }
        // Get parent products for filter dropdown
        $products = $this->productService->search(['parentId' => null], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index', compact('products'));
    }

    public function create(): View
    {
        // Get all parent products for dropdown
        $products = $this->productService->search(['parentId' => null], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('products'));
    }

    public function show($id): View
    {
        $row = $this->service->find((int) $id, $this->relations);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.show', compact('row'));
    }

    public function edit($id): View
    {
        $row = $this->service->find((int) $id, $this->relations);
        // Get all parent products for dropdown
        $products = $this->productService->search(['parentId' => null], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'products'));
    }

    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->service->create($data);
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateRequest $request, Product $child_product): JsonResponse
    {
        try {
            $this->service->update($child_product, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function toggleField(Request $request, $childProduct, $key)
    {
        return $this->service->toggleField($childProduct, $key);
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

            // Add filter for child products only
            $filters['parentId'] = 'not_null';

            $export = $this->exportService->createExport(
                name: __('trans.child-product.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'ChildProduct',
                parameters: $filters
            );

            ExportJob::dispatch(
                export: $export,
                exportClass: ChildProductExport::class,
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
