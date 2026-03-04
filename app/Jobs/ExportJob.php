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
            $exportInstance = new $this->exportClass($data, $this->export->id);
 
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
        $page = false;
        $limit = false;

        // Child products are Product models with parent_id set (no separate ChildProduct model)
        if ($this->export->model === 'ChildProduct') {
            $service = app('App\\Services\\ProductService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            // $filters['parentId'] = 'not_null';
            $filteredData = $service->fresh()->search(
                $filters,
                ['parent', 'parent.store', 'parent.category', 'parent.brand'],
                ['page' => $page, 'limit' => $limit]
            );

            // dd($filteredData->toArray()[0]['parent']['name'][app()->getLocale()]);
 
            return $filteredData->toArray();
        }

        $modelClass = 'App\\Models\\' . $this->export->model;

        if (!class_exists($modelClass)) {
            throw new Exception(__('trans.model_not_found', ['model' => $modelClass]));
        }

        if ($modelClass === 'App\\Models\\Admin') {
            $service = app('App\\Services\\AdminService');
            $filteredData = $service->fresh()->search(
                $this->filters,
                [],
                ['page' => $page, 'limit' => $limit]
            );
            return $filteredData->toArray();
        }

        if ($modelClass === 'App\\Models\\User') {
            $service = app('App\\Services\\UserService');
            $filteredData = $service->fresh()->search(
                $this->filters,
                ['country'],
                ['page' => $page, 'limit' => $limit]
            );
            return $filteredData->toArray();
        }

        if ($modelClass === 'App\\Models\\Product') {
            $service = app('App\\Services\\ProductService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filters['parent'] = true;
            $filteredData = $service->fresh()->search($filters, ['store', 'category', 'brand']
            );
            return $filteredData->toArray();
        }

        // stores
        if ($modelClass === 'App\\Models\\Store') {
            $service = app('App\\Services\\StoreService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filteredData = $service->fresh()->search($filters, ['city']);
            return $filteredData->toArray();
        }

        // categories
        if ($modelClass === 'App\\Models\\Category') {
            $service = app('App\\Services\\CategoryService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filteredData = $service->fresh()->search($filters, []);
            return $filteredData->toArray();
        }

        // brands
        if ($modelClass === 'App\\Models\\Brand') {
            $service = app('App\\Services\\BrandService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filteredData = $service->fresh()->search($filters, []);
            return $filteredData->toArray();
        }
        
        // slider
        if ($modelClass === 'App\\Models\\Slider') {
            $service = app('App\\Services\\SliderService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filteredData = $service->fresh()->search($filters, []);
            return $filteredData->toArray();
        }

        // intro
        if ($modelClass === 'App\\Models\\Intro') {
            $service = app('App\\Services\\IntroService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filteredData = $service->fresh()->search($filters, []);
            return $filteredData->toArray();
        }


        // orders
        if ($modelClass === 'App\\Models\\Order') {
            $service = app('App\\Services\\OrderService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filteredData = $service->fresh()->search($filters, ['user', 'store']);
            return $filteredData->toArray();
        }


        // contact us
        if ($modelClass === 'App\\Models\\ContactUs') {
            $service = app('App\\Services\\ContactUsService');
            $filters = $this->filters;
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filteredData = $service->fresh()->search($filters, []);
            return $filteredData->toArray();
        }
 

        $serviceClass = 'App\\Services\\' . class_basename($modelClass) . 'Service';

        if (class_exists($serviceClass)) {
            try {
                $service = app($serviceClass);
                if (method_exists($service, 'fresh') && method_exists($service, 'search')) {
                    $filteredData = $service->fresh()->search(
                        $this->filters,
                        [],
                        ['page' => $page, 'limit' => $limit]
                    );
                    return $filteredData->toArray();
                }
            } catch (Exception $e) {
                // Fall through to basic query if service fails
                Log::warning("Service {$serviceClass} failed, using basic query: " . $e->getMessage());
            }
        }

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
