<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group Api
 * @subgroup Profile
 */
class ChangePasswordController extends BaseApiController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Update Profile.
     * @authenticated
     * @bodyParam old_password string required
     * @bodyParam new_password string required
     * @bodyParam new_password_confirmation string required
     * @return JsonResponse
     */

     
    public function update(ChangePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if (!Hash::check($data['old_password'], $user->password)) {
            return $this->errorWrongArgs(trans('auth.old_password_incorrect'));
        }

        $this->service->update($user, [
            'password' => $data['new_password']
        ]);

        return $this->respondWithSuccess(trans('trans.messages.password_changed_successfully'), [
            'user' => new UserResource($user)
        ]);
    }
}
