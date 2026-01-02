<?php

namespace App\Services;

use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\AdminContract;
use Exception;
use Illuminate\Support\Facades\DB;

class AdminService extends BaseService
{

    protected BaseContract $repository;

    public RoleService $roleService;

    public function __construct(AdminContract $repository, RoleService $roleService){
        $this->roleService = $roleService;
        $this->repository  = $repository;
        parent::__construct($repository);
    }

    public function create($request){
        DB::beginTransaction();
        $object = $this->repository->create($request);
        if (@$request['role_id']) {
            $role = $this->roleService->find($request['role_id']);
            $object->assignRole($role);
        }
        DB::commit();
        return $object;
    }

    public function update($admin, $request){

        DB::beginTransaction();
        $this->repository->update($admin, $request);

        if (array_key_exists('role_id', $request)) {
            if ($request['role_id']) {
                $role = $this->roleService->find($request['role_id']);
                $admin->syncRoles([$role]);
            } else {
                $admin->syncRoles([]);
            }
        }

        DB::commit();
        return $admin;
    }

    public function remove($admin){
        DB::beginTransaction();
        $removedItem = $this->repository->remove($admin);
        DB::commit();
        return $removedItem;
    }

    public function bulkAction($action, $ids)
    {
        DB::beginTransaction();
        
        try {
            $admins = $this->repository->getByKey('id', $ids);
            $count = 0;
            
            foreach ($admins as $admin) {
                switch ($action) {
                    case 'activate':
                        $admin->update(['is_active' => true]);
                        $count++;
                        break;
                    case 'deactivate':
                        $admin->update(['is_active' => false]);
                        $count++;
                        break;
                    case 'block':
                        $admin->update(['is_blocked' => true]);
                        $count++;
                        break;
                    case 'unblock':
                        $admin->update(['is_blocked' => false]);
                        $count++;
                        break;
                }
            }
            
            DB::commit();
            
            // Prepare response message based on action
            $messages = [
                'activate' => __('trans.bulk_activate_success', ['count' => $count]),
                'deactivate' => __('trans.bulk_deactivate_success', ['count' => $count]),
                'block' => __('trans.bulk_block_success', ['count' => $count]),
                'unblock' => __('trans.bulk_unblock_success', ['count' => $count])
            ];
            
            return [
                'count' => $count,
                'message' => $messages[$action] ?? __('trans.bulk_action_success')
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
