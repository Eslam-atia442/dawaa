<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Events\ExportCompleted;
use App\Models\Export;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ExportService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ReportExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 3;

    protected Export $export;

    protected string $exportClass;

    protected string $reportType;

    protected array $filters;

    public function __construct(Export $export, string $exportClass, string $reportType, array $filters = [])
    {
        $this->export = $export;
        $this->exportClass = $exportClass;
        $this->reportType = $reportType;
        $this->filters = $filters;
    }

    public function handle(ExportService $exportService): void
    {
        try {
            Log::info("Starting report export job for export ID: {$this->export->id}, type: {$this->reportType}");

            $data = $this->getReportData();

            $filename = $this->generateFilename();
            $tempPath = 'temp/'.$filename;

            $exportInstance = new $this->exportClass($data);

            Excel::store($exportInstance, $tempPath, 'local');

            $permanentPath = 'exports/'.$filename;
            Storage::move($tempPath, $permanentPath);

            $exportService->markAsReady($this->export, $permanentPath, count($data));

            event(new ExportCompleted($this->export));

            Log::info("Report export job completed successfully for export ID: {$this->export->id}");

        } catch (Exception $e) {
            Log::error("Report export job failed for export ID: {$this->export->id}. Error: ".$e->getMessage());
            $exportService->markAsFailed($this->export, $e->getMessage());
            throw $e;
        }
    }

    protected function getReportData(): array
    {
        return match ($this->reportType) {
            'best-sellers' => $this->getBestSellersData(),
            'stock-quantity' => $this->getStockQuantityData(),
            default => [],
        };
    }

    protected function getBestSellersData(): array
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

        if (! empty($this->filters['store'])) {
            $query->whereHas('product', function ($q) {
                $q->whereIn('store_id', (array) $this->filters['store']);
            });
        }

        if (! empty($this->filters['user'])) {
            $query->whereHas('order', function ($q) {
                $q->whereIn('user_id', (array) $this->filters['user']);
            });
        }

        if (! empty($this->filters['keyword'])) {
            $keyword = $this->filters['keyword'];
            $productIds = Product::where('name', 'like', "%{$keyword}%")->pluck('id');
            $query->whereIn('order_items.product_id', $productIds);
        }

        if (! empty($this->filters['createdAtMin'])) {
            $query->where('orders.created_at', '>=', $this->filters['createdAtMin']);
        }

        if (! empty($this->filters['createdAtMax'])) {
            $query->where('orders.created_at', '<=', $this->filters['createdAtMax']);
        }

        $orderDirection = $this->filters['order'] ?? 'DESC';
        $results = $query->orderBy('total_quantity_sold', $orderDirection)->get();

        return $results->map(function ($item) {
            $product = Product::find($item->product_id);

            return [
                'product_name' => $product ? $product->name : '-',
                'total_quantity_sold' => $item->total_quantity_sold,
                'orders_count' => $item->orders_count,
                'total_revenue' => $item->total_revenue,
            ];
        })->toArray();
    }

    protected function getStockQuantityData(): array
    {
        $quantity = $this->filters['quantity'] ?? null;

        if ($quantity === null || $quantity === '') {
            return [];
        }

        $query = Product::query()
            ->whereNotNull('parent_id')
            ->where('quantity', '<=', (int) $quantity)
            ->with(['parent', 'store'])
            ->when(! empty($this->filters['store']), function ($query) {
                $query->ofStore($this->filters['store']);
            })
            ->when(! empty($this->filters['keyword']), function ($query) {
                $query->ofKeyword($this->filters['keyword']);
            });

        if (! empty($this->filters['keyword'])) {
            $keyword = $this->filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhereHas('parent', function ($pq) use ($keyword) {
                        $pq->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query->get()->map(function ($product) {
            return [
                'product_name' => $product->name,
                'parent_product_name' => $product->parent->name ?? '-',
                'quantity' => $product->quantity,
                'price' => $product->price,
                'store_name' => $product->store->name ?? '-',
                'expiry_date' => $product->expiry_date?->format('Y-m-d'),
                'production_line_number' => $product->production_line_number ?? '-',
            ];
        })->toArray();
    }

    protected function generateFilename(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $exportId = $this->export->id;

        return "{$this->reportType}_report_{$exportId}_{$timestamp}.xlsx";
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Report export job permanently failed for export ID: {$this->export->id}. Error: ".$exception->getMessage());
    }
}
