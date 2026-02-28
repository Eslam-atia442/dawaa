<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\ContactUs\CreateRequest;
use App\Http\Requests\Admin\ContactUs\UpdateRequest;
use App\Models\ContactUs;
use App\Exports\ContactUsExport;
use App\Jobs\ExportJob;
use App\Services\ExportService;
use Exception;
use App\Services\ContactUsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ContactUsController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;
    protected ExportService $exportService;

    public function __construct(
                        ContactUsService $service,
                        ExportService $exportService,
                        $table = 'contactuses',
                        $guard = 'admin'
    )
    {
        $this->service = $service;
        $this->exportService = $exportService;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['country'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations ,'contact-us');
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
    public function update(UpdateRequest $request, ContactUs $contactUs): JsonResponse
    {
        try {
            $this->service->update($contactUs, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }

    public function toggleField(Request $request, $contactUs, $key)
    {
        return $this->service->toggleField($contactUs, $key);
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
                name: __('trans.contactUs.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'ContactUs',
                parameters: $filters
            );

            ExportJob::dispatch(
                export: $export,
                exportClass: ContactUsExport::class,
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
