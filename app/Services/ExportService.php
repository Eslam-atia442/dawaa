<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\ExportContract;
use App\Models\Export;
use Exception;
use Illuminate\Support\Facades\Auth;

class ExportService extends BaseService
{
    protected BaseContract $repository;

    public function __construct(ExportContract $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    public function createExport(string $name, string $model, array $parameters = []): Export
    {
        return $this->repository->create([
            'name' => $name,
            'model' => $model,
            'user_id' => Auth::guard('admin')->id(),
            'parameters' => $parameters,
            'status' => 'processing'
        ]);
    }

    public function markAsReady(Export $export, string $filePath, int $totalRecords = 0): bool
    {
        return $export->update([
            'status' => 'ready',
            'file_path' => $filePath,
            'total_records' => $totalRecords,
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(Export $export, string $errorMessage = null): bool
    {
        return $export->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    public function search($filters = [], $relations = [], $data = [])
    {
        // Always filter exports by current user for security
        $filters['user'] = Auth::guard('admin')->id();

        return parent::search($filters, $relations, $data);
    }

    public function getReadyExports()
    {
        return $this->repository->search([
            'status' => 'ready',
            'user' => Auth::guard('admin')->id()
        ]);
    }

    public function getProcessingExports()
    {
        return $this->repository->search([
            'status' => 'processing',
            'user' => Auth::guard('admin')->id()
        ]);
    }
}
