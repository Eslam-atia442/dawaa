<?php

namespace App\Repositories\SQL;

use App\Models\Permission;
use App\Models\Role;
use App\Repositories\Contracts\RoleContract;
use Illuminate\Support\Arr;

class RoleRepository extends BaseRepository implements RoleContract
{
    /**
     * RoleRepository constructor.
     * @param Role $model
     */
    public PermissionRepository $permissionRepository;

    public function __construct(Role $model, PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
        parent::__construct($model);
    }

    public function create(array $attributes = []): mixed
    {
        $role = parent::create($attributes);
        $permissions = $this->permissionRepository->getByKey('id', $attributes['permissions']);
        $parent_permissions = $permissions->unique('model')->pluck('model')->toArray();
        $read_all_permissions = Permission::whereIn('model', $parent_permissions)->
        where('action', 'read-all')->get()->pluck('id')->toArray();
        $read_all_permissions = $this->permissionRepository->freshRepo()->getByKey('id', $read_all_permissions);
        $permissions = $read_all_permissions->merge($permissions);
        $role->givePermissionTo($permissions);
        return $role->refresh();
    }

    public function update($model, array $attributes = []): mixed
    {
        $role = parent::update($model, $attributes);
        $permissions = $this->permissionRepository->getByKey('id', $attributes['permissions']);
        $role->syncPermissions($permissions);
        return $role->refresh();
    }

    public function syncPermissions($model, array $attributes = []): mixed
    {
        $requestPermissions = $attributes['role_permissions'] ?
            array_filter(Arr::flatten(array_values($attributes['role_permissions']))) : [];
        $model->syncPermissions($requestPermissions);
        return $model->refresh();
    }


    public function toggleField($model, string $field): mixed
    {
        $newVal = 1;
        if ($model[$field] == 1) {
            $newVal = 0;
        }
        return $model->update([$field => $newVal]);
    }


}
