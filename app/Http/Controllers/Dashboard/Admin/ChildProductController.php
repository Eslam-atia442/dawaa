<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
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


class ChildProductController extends Controller
{
    public object           $service;
    public object           $productService;
    public string           $table;
    public string           $guard;
    public array            $relations;
    protected ExportService $exportService;

    public function __construct(
        ChildProductService $service,
        ProductService      $productService,
        ExportService       $exportService,
                            $table = 'child-products',
                            $guard = 'admin'
    )
    {
        $this->service        = $service;
        $this->productService = $productService;
        $this->exportService  = $exportService;
        $this->table          = $table;
        $this->guard          = $guard;
        $this->relations      = ['parent', 'parent.store', 'parent.city', 'parent.category', 'parent.brand'];
        
        // Apply permissions
        $this->middleware('permission:read-all-child-product')->only(['index']);
        $this->middleware('permission:read-child-product')->only(['show']);
        $this->middleware('permission:create-child-product')->only(['create', 'store']);
        $this->middleware('permission:update-child-product')->only(['edit', 'update']);
        $this->middleware('permission:delete-child-product')->only(['destroy', 'destroyMultiple']);
    }

    public function index(Product $product): View|JsonResponse
    {
        if (request()->ajax()) {
            // Only show child products for this parent
            $filters = array_merge(request()->all(), ['parentId' => $product->id]);
            $rows    = $this->service->search($filters, $this->relations);
            $html    = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows', 'product'))->render();
            return response()->json(['html' => $html]);
        }
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index', compact('product'));
    }

    public function create(Product $product): View
    {
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('product'));
    }

    public function show(Product $product, Product $childProduct): View
    {
        $row = $this->service->find($childProduct->id, $this->relations);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.show', compact('row', 'product'));
    }

    public function edit(Product $product, Product $childProduct): View
    {
        $row = $this->service->find($childProduct->id, $this->relations);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'product'));
    }

    public function store(CreateRequest $request, Product $product): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['parent_id'] = $product->id;
            $this->service->create($data);
            return response()->json(['url' => route('admin.products.child-products.index', $product)]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateRequest $request, Product $product, Product $childProduct): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['parent_id'] = $product->id;
            $this->service->update($childProduct, $data);
            return response()->json(['url' => route('admin.products.child-products.index', $product)]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(Product $product, Product $childProduct): JsonResponse
    {
        try {
            $this->service->delete($childProduct);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function toggleField(Request $request, Product $product, $childProduct, $key)
    {
        return $this->service->toggleField($childProduct, $key);
    }

    public function destroyMultiple(Request $request, Product $product)
    {
        $request->validate([
                               'data' => 'required|json'
                           ]);

        $ids = json_decode($request->input('data'), true);
        
        try {
            foreach ($ids as $id) {
                $childProduct = $this->service->find($id);
                if ($childProduct && $childProduct->parent_id == $product->id) {
                    $this->service->delete($childProduct);
                }
            }
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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
                name      : __('trans.child-product.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model     : 'ChildProduct',
                parameters: $filters
            );

            ExportJob::dispatch(
                export     : $export,
                exportClass: ChildProductExport::class,
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
