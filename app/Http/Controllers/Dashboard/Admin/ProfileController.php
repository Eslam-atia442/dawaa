<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Http\Requests\Admin\Profile\ChangePasswordRequest;
use App\Services\AdminService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        AdminService $service,
                     $table = 'admins',
                     $guard = 'admin'
    )
    {
        $this->service   = $service;
        $this->table     = $table;
        $this->guard     = $guard;
        $this->relations = [];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'admin');
    }

    /**
     * Show the admin profile page
     */


    public function index(): View
    {
        $admin = auth('admin')->user();
        return view('dashboard.admin.profile.index', compact('admin'));
    }

    /**
     * Update admin profile information
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $admin = auth('admin')->user();
            $data  = $request->validated();

            unset($data['old_password'], $data['new_password'], $data['new_password_confirmation']);
            $this->service->update($admin, $data);
            return response()->json([
                'message' => trans('trans.messages.profile_updated_successfully'),
                'url'     => route('admin.profile.index')
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Change admin password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $admin = auth('admin')->user();
            $data  = $request->validated();

            if (!Hash::check($data['old_password'], $admin->password)) {
                return response()->json([
                    'message' => trans(__('auth.old_password_incorrect')),
                ],400);
            }
            // Update password
            $admin->update([
                'password' => Hash::make($data['new_password'])
            ]);

            return response()->json([
                'message' => trans('trans.messages.password_changed_successfully')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
