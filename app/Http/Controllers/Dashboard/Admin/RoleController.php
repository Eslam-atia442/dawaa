<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Role\CreateRequest;
use App\Http\Requests\Admin\Role\UpdateRequest;
use App\Models\Role;
use App\Services\PermissionService;
use Exception;
use App\Services\RoleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class RoleController extends BaseWebController
{
    public object $service;
    public object $permissionService;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        RoleService       $service,
        PermissionService $permissionService,
                          $table = 'roles',
                          $guard = 'admin'
    )
    {
        $this->permissionService = $permissionService;
        $this->service           = $service;
        $this->table             = $table;
        $this->guard             = $guard;
        $this->relations         = [];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'role');
    }

    public function show($id): View
    {
        $row              = $this->service->find($id, $this->relations);
        $role_permissions = $row->permissions;
        request()->merge(['page' => false, 'limit' => false]);
        $permissions = $this->permissionService->search(request()->all(), []);

        $permissions = collect($permissions)->groupBy('model')->toArray();

        return view('dashboard.' . $this->guard . '.' . $this->table . '.show', compact('row', 'role_permissions', 'permissions'));
    }


    public function create(): View
    {
        request()->merge(['page' => false, 'limit' => false]);
        $permissions = $this->permissionService->search(request()->all(), []);

        $permissions = collect($permissions)->groupBy('model')->toArray();
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('permissions'));
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

    public function edit($role): View
    {
        $row = $this->service->find($role);
        request()->merge(['page' => false, 'limit' => false]);
        $permissions = $this->permissionService->search(request()->all(), []);
        $permissions = collect($permissions)->groupBy('model')->toArray();

        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('permissions', 'row'));
    }

    public function update(UpdateRequest $request, Role $role): JsonResponse
    {
        try {
            $this->service->update($role, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function toggleField(Request $request, $role, $key)
    {
        return $this->service->toggleField($role, $key);
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'data' => 'required|json'
        ]);

        return $this->destroy($request->input('data'));
    }
}
