<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Admin\CreateRequest;
use App\Http\Requests\Admin\Admin\UpdateRequest;
use App\Models\Admin;
use App\Services\RoleService;
use App\Services\ExportService;
use App\Jobs\ExportJob;
use App\Exports\AdminExport;
use Exception;
use App\Services\AdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AdminController extends BaseWebController
{
    public object $service;
    public object $roleService;
    public string $table;
    public string $guard;
    public array $relations;
    protected ExportService $exportService;

    public function __construct(
        AdminService $service,
        RoleService  $roleService,
        ExportService $exportService,
        $table = 'admins',
        $guard = 'admin'
    ) {
        $this->service       = $service;
        $this->roleService   = $roleService;
        $this->exportService = $exportService;
        $this->table         = $table;
        $this->guard         = $guard;
        $this->relations     = ['wallet'];

        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'admin');
    }

    public function index(): View|JsonResponse
    {
        $blocked_admins = $this->service->search(['isBlocked' => true], [], ['page' => false, 'limit' => false])->count();
        $active_admins  = $this->service->fresh()->search(['isActive' => true], [], ['page' => false, 'limit' => false])->count();
        $admins_count   = $this->service->fresh()->search([], [], ['page' => false, 'limit' => false])->count();

        if (request()->ajax()) {
            $rows = $this->service->fresh()->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json([
                'html'                 => $html,
                'blocked_admins_count' => $blocked_admins,
                'active_admins_count'  => $active_admins,
                'admins_count'         => $admins_count
            ]);
        }
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index', compact('admins_count', 'blocked_admins', 'active_admins'));
    }

    public function create(): View
    {
        $roles = $this->roleService->search([], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('roles'));
    }

    public function edit($id): View
    {
        $row   = $this->service->find($id, $this->relations);
        $roles = $this->roleService->search([], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'roles'));
    }

    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $this->service->create($request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateRequest $request, Admin $admin): JsonResponse
    {

        try {
            $this->service->update($admin, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function toggleField(Request $request, $admin, $key)
    {
        return $this->service->toggleField($admin, $key);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,block,unblock',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:admins,id'
        ]);

        try {
            $result = $this->service->bulkAction($request->input('action'), $request->input('ids'));

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'updated_count' => $result['count']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
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
                ->filter(fn($value) => $value !== '' && $value !== null)
                ->toArray();

            $export = $this->exportService->createExport(
                name: __('trans.admin.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'Admin',
                parameters: $filters
            );

            ExportJob::dispatch(
                export: $export,
                exportClass: AdminExport::class,
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
