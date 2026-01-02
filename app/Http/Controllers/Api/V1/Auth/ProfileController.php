<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Api
 * @subgroup Profile
 */
class ProfileController extends BaseApiController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Update Profile.
     * @authenticated
     * @bodyParam name string optional
     * @bodyParam email string optional
     * @bodyParam phone string optional
     * @bodyParam gender string optional
     * @bodyParam dob date optional Format: Y-m-d
     * @bodyParam country_id int optional
     * @bodyParam avatar file optional Image file (jpeg,png,jpg,gif)
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $user  = $this->service->update($user, $data);
        $user->refresh();
        return $this->respondWithSuccess(
            trans('trans.messages.profile_updated_successfully'),
            [
                'user' => new UserResource($user)
            ]
        );
    }

    /**
     * Delete User Account.
     * @authenticated
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->tokens()->delete();
        $this->service->remove($user);
        return $this->respondWithSuccess(
            trans('trans.messages.account_deleted_successfully')
        );
    }
}

