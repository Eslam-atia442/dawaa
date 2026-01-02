<?php

namespace App\Services;

use App\Enums\NotificationEnum;
use App\Notifications\GeneralNotification;
use App\Repositories\Contracts\AdminContract;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\UserContract;
use Illuminate\Support\Facades\DB;
use Exception;

class UserService extends BaseService
{

    protected BaseContract $repository;

    public function __construct(UserContract $repository, public AdminContract $adminRepository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    public function create($request)
    {
        DB::beginTransaction();
        $object = $this->repository->create($request);
        DB::commit();
        return $object;
    }

    public function update($user, $request)
    {
        return $this->repository->update($user, $request);
    }

    public function remove($user)
    {
        return $this->repository->remove($user);
    }

    public function adminNotification($user): bool
    {
        $admins   = $this->adminRepository->search(filters: ['limit' => false, 'page' => false], relations: [], data: []);
        $messages = [
            'message_ar' => $user->name . '  تم تسجيل دخول المستخدم بنجاح',
            'message_en' => $user->name . ' has logged in successfully ',
        ];

        $type    = NotificationEnum::user_login->value;
        $model   = $user->getMorphClass();
        $modelId = $user->id;
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification($messages, $type, $model, $modelId));
        }

        return true;
    }

    public function findBySocialCredentials($email, $socialType, $socialToken = null)
    {
        $fields = [
            'email' => $email,
            'social_type' => $socialType,
        ];

        if ($socialToken) {
            $fields['social_token'] = $socialToken;
        }

        return $this->repository->findByFields($fields);
    }

    public function bulkAction($action, $ids)
    {
        DB::beginTransaction();

        try {
            $users = $this->repository->getByKey('id', $ids);
            $count = 0;

            foreach ($users as $user) {
                switch ($action) {
                    case 'activate':
                        $user->update(['is_active' => true]);
                        $count++;
                        break;
                    case 'deactivate':
                        $user->update(['is_active' => false]);
                        $count++;
                        break;
                    case 'block':
                        $user->update(['is_blocked' => true]);
                        $count++;
                        break;
                    case 'unblock':
                        $user->update(['is_blocked' => false]);
                        $count++;
                        break;
                }
            }

            DB::commit();

            // Prepare response message based on action
            $messages = [
                'activate' => __('trans.bulk_activate_users_success', ['count' => $count]),
                'deactivate' => __('trans.bulk_deactivate_users_success', ['count' => $count]),
                'block' => __('trans.bulk_block_users_success', ['count' => $count]),
                'unblock' => __('trans.bulk_unblock_users_success', ['count' => $count])
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
