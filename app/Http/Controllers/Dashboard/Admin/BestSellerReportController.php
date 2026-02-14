<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enums\OrderStatusEnum;
use App\Exports\BestSellerReportExport;
use App\Http\Controllers\Controller;
use App\Jobs\ReportExportJob;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ExportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class BestSellerReportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
        $this->middleware('permission:best-seller-report-setting');
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->getReportData(request()->all());
            $html = view('dashboard.admin.reports.best-sellers.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }

        return view('dashboard.admin.reports.best-sellers.index');
    }

    protected function getReportData(array $filters): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = OrderItem::query()
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', OrderStatusEnum::PAID->value)
            ->whereNull('orders.parent_id')
            ->whereNull('orders.deleted_at')
            ->whereNull('order_items.deleted_at')
            ->whereNotNull('order_items.order_id')
            ->groupBy('order_items.product_id');

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $productIds = Product::where('name', 'like', "%{$keyword}%")->pluck('id');
            $query->whereIn('order_items.product_id', $productIds);
        }

        if (!empty($filters['createdAtMin'])) {
            $query->where('orders.created_at', '>=', $filters['createdAtMin']);
        }

        if (!empty($filters['createdAtMax'])) {
            $query->where('orders.created_at', '<=', $filters['createdAtMax']);
        }

        $orderDirection = $filters['order'] ?? 'DESC';
        $query->orderBy('total_quantity_sold', $orderDirection);

        $page    = $filters['page'] ?? 1;
        $perPage = $filters['limit'] ?? 15;

        $results = $query->paginate($perPage, ['*'], 'page', $page);

        // Load product names
        $productIds = $results->pluck('product_id');
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $results->getCollection()->transform(function ($item) use ($products) {
            $item->product = $products[$item->product_id] ?? null;
            return $item;
        });

        return $results;
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
                name: __('trans.best_seller_report') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'Setting',
                parameters: $filters
            );

            ReportExportJob::dispatch(
                export: $export,
                exportClass: BestSellerReportExport::class,
                reportType: 'best-sellers',
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
