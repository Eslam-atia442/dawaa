<?php

namespace App\Jobs;

use App\Events\ExportCompleted;
use App\Models\Export;
use App\Services\ExportService;
use Exception;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // 1 hour timeout
    public int $tries   = 3;

    protected Export $export;
    protected string $exportClass;
    protected array  $filters;

    public function __construct(Export $export, string $exportClass, array $filters = [])
    {
        $this->export      = $export;
        $this->exportClass = $exportClass;
        $this->filters     = $filters;
    }

    public function handle(ExportService $exportService): void
    {
        try {
            Log::info("Starting export job for export ID: {$this->export->id}");

            $data = $this->getExportData();

            // Allow export even with no data - will show empty table with headers

            $filename = $this->generateFilename();
            $tempPath = 'temp/' . $filename;

            // Create export instance with appropriate parameters
            $exportInstance = $this->createExportInstance($data);

            Excel::store($exportInstance, $tempPath, 'local');

            $permanentPath = 'exports/' . $filename;
            Storage::move($tempPath, $permanentPath);

            $exportService->markAsReady($this->export, $permanentPath, count($data));

            event(new ExportCompleted($this->export));

            Log::info("Export job completed successfully for export ID: {$this->export->id}");

        } catch (Exception $e) {
            Log::error("Export job failed for export ID: {$this->export->id}. Error: " . $e->getMessage());
            $exportService->markAsFailed($this->export, $e->getMessage());
            throw $e;
        }
    }

    protected function getExportData(): array
    {
        $modelClass = 'App\\Models\\' . $this->export->model;

        if (!class_exists($modelClass)) {
            throw new Exception(__('trans.model_not_found', ['model' => $modelClass]));
        }

        // Use the appropriate service to get filtered data
        if ($modelClass === 'App\\Models\\GoldFund') {
            $serviceClass = 'App\\Services\\GoldFundService';
            if (!class_exists($serviceClass)) {
                throw new Exception(__('trans.model_not_found', ['model' => $serviceClass]));
            }

            $service = app($serviceClass);
            
            // Fresh the repository to ensure clean query, then search with filters
            $filteredData = $service->fresh()->search(
                $this->filters, 
                ['riskLevel', 'assetManagementCompany', 'currency', 'goldFundType'], 
                ['page' => false]
            );

            return $filteredData->toArray();
        }

        // Handle GoldFundPriceLog export
        if ($modelClass === 'App\\Models\\GoldFundPriceLog') {
            $goldFundId = $this->filters['gold_fund_id'] ?? null;
            
            if (!$goldFundId) {
                throw new Exception(__('trans.gold_fund_id_required'));
            }

            $priceLogs = $modelClass::where('gold_fund_id', $goldFundId)
                ->orderBy('changed_at', 'desc')
                ->get()
                ->toArray();

            return $priceLogs;
        }

        // Handle Admin export
        if ($modelClass === 'App\\Models\\Admin') {
            $service = app('App\\Services\\AdminService');
            $filteredData = $service->fresh()->search(
                $this->filters,
                [],
                ['page' => false]
            );
            return $filteredData->toArray();
        }

        // Handle User export
        if ($modelClass === 'App\\Models\\User') {
            $service = app('App\\Services\\UserService');
            $filteredData = $service->fresh()->search(
                $this->filters,
                ['country'],
                ['page' => false]
            );
            return $filteredData->toArray();
        }

        // Handle Certificate export
        if ($modelClass === 'App\\Models\\Certificate') {
            $service = app('App\\Services\\CertificateService');
            $filteredData = $service->fresh()->search(
                $this->filters,
                ['bank', 'certificationType', 'riskLevel'],
                ['page' => false]
            );
            return $filteredData->toArray();
        }

        // Fallback for other models - try to use service if exists
        $serviceClass = 'App\\Services\\' . class_basename($modelClass) . 'Service';
        
        if (class_exists($serviceClass)) {
            try {
                $service = app($serviceClass);
                if (method_exists($service, 'fresh') && method_exists($service, 'search')) {
                    $filteredData = $service->fresh()->search(
                        $this->filters,
                        [],
                        ['page' => false]
                    );
                    return $filteredData->toArray();
                }
            } catch (Exception $e) {
                // Fall through to basic query if service fails
                Log::warning("Service {$serviceClass} failed, using basic query: " . $e->getMessage());
            }
        }

        // Basic fallback using query scopes
        $query = $modelClass::query();

        if (!empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                // Skip empty values
                if (empty($value) && $value !== '0' && $value !== 0) {
                    continue;
                }
                
                if (method_exists($query, 'of' . ucfirst($filter))) {
                    $query->{'of' . ucfirst($filter)}($value);
                } elseif (method_exists($query, 'where' . ucfirst($filter))) {
                    $query->{'where' . ucfirst($filter)}($value);
                }
            }
        }

        return $query->get()->toArray();
    }

    protected function createExportInstance(array $data): object
    {
        // Handle GoldFundPriceLogExport which needs fund_name parameter
        if ($this->exportClass === 'App\\Exports\\GoldFundPriceLogExport') {
            $fundName = $this->filters['fund_name'] ?? '';
            return new $this->exportClass($data, $fundName);
        }

        // Default: just pass data
        return new $this->exportClass($data);
    }

    protected function generateFilename(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $modelName = strtolower($this->export->model);
        $exportId  = $this->export->id;

        return "{$modelName}_export_{$exportId}_{$timestamp}.xlsx";
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("Export job permanently failed for export ID: {$this->export->id}. Error: " . $exception->getMessage());

        // The export is already marked as failed in the handle method
    }
}
