<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\StockQuantityReportExport;
use App\Http\Controllers\Controller;
use App\Jobs\ReportExportJob;
use App\Models\Product;
use App\Services\ExportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Repositories\SQL\StoreRepository;
use App\Repositories\SQL\UserRepository;

class StockQuantityReportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
        $this->middleware('permission:stock-quantity-report-setting');
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->getReportData(request()->all());
            $html = view('dashboard.admin.reports.stock-quantity.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }

        $stores = app(StoreRepository::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);

        return view('dashboard.admin.reports.stock-quantity.index', compact('stores'));
    }

    protected function getReportData(array $filters)
    {
        $quantity = $filters['quantity'] ?? null;

        // Only return data if user entered a quantity filter
        if ($quantity === null || $quantity === '') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        $query = Product::query()
            ->whereNotNull('parent_id')
            ->where('quantity', '<=', (int)$quantity)
            ->with(['parent', 'store']);

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhereHas('parent', function ($pq) use ($keyword) {
                      $pq->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        if (!empty($filters['store'])) {
            $query->whereHas('store', function ($query) use ($filters) {
                $query->whereIn('store_id', $filters['store']);
            });
        }

        if (!empty($filters['user'])) {
            $query->whereHas('store', function ($query) use ($filters) {
                $query->whereIn('user_id', $filters['user']);
            });
        }

        $orderDirection = $filters['order'] ?? 'ASC';
        $query->orderBy('quantity', $orderDirection);

        $page    = $filters['page'] ?? 1;
        $perPage = $filters['limit'] ?? 15;

        return $query->paginate($perPage, ['*'], 'page', $page);
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

            if (empty($filters['quantity'])) {
                return response()->json(['error' => __('trans.no_quantity_filter')], 400);
            }

            $export = $this->exportService->createExport(
                name: __('trans.stock_quantity_report') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'Setting',
                parameters: $filters
            );

            ReportExportJob::dispatch(
                export: $export,
                exportClass: StockQuantityReportExport::class,
                reportType: 'stock-quantity',
                filters: $filters
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
