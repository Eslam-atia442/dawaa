<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Models\Export;
use App\Services\ExportService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        ExportService $service,
        $table = 'exports',
        $guard = 'admin'
    ) {
        $this->service = $service;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['user'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'export');
    }



    public function index(): View|JsonResponse
    {

        if (request()->ajax()) {
            $rows = $this->service->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html  ]);
        }
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index');
    }

    public function download(Export $export): StreamedResponse
    {
        if ($export->user_id !== auth($this->guard)->id()) {
            abort(403, __('trans.unauthorized'));
        }

        if (!$export->isReady()) {
            abort(404, __('trans.export_not_ready'));
        }

        if (!Storage::exists($export->file_path)) {
            abort(404, __('trans.file_not_found'));
        }

        return Storage::download($export->file_path, $export->file_name);
    }

    public function destroy($ids): JsonResponse
    {
        try {
            $exportIds = is_array($ids) ? $ids : [$ids];

            foreach ($exportIds as $exportId) {
                $export = $this->service->find($exportId);

                if ($export->user_id !== auth($this->guard)->id()) {
                    return response()->json(['error' => __('trans.unauthorized')], 403);
                }

                if ($export->file_path && Storage::exists($export->file_path)) {
                    Storage::delete($export->file_path);
                }
            }

            return parent::destroy($ids);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
