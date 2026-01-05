<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Intro\CreateRequest;
use App\Http\Requests\Admin\Intro\UpdateRequest;
use App\Models\Intro;
use App\Exports\IntroExport;
use App\Jobs\ExportJob;
use App\Services\ExportService;
use Exception;
use App\Services\IntroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class IntroController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;
    protected ExportService $exportService;

    public function __construct(
                        IntroService $service,
                        ExportService $exportService,
                        $table = 'intros',
                        $guard = 'admin'
    )
    {
        $this->service = $service;
        $this->exportService = $exportService;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = [];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations ,'intro');
    }
    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $this->service->create($request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }
    public function update(UpdateRequest $request, Intro $intro): JsonResponse
    {
        try {
            $this->service->update($intro, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }

    public function toggleField(Request $request, $intro, $key)
    {
        return $this->service->toggleField($intro, $key);
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

            $export = $this->exportService->createExport(
                name: __('trans.intro.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'Intro',
                parameters: $filters
            );

            ExportJob::dispatch(
                export: $export,
                exportClass: IntroExport::class,
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
