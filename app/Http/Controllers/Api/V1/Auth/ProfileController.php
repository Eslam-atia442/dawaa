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
     * @bodyParam name string required example: Doctor Name or Pharmacy Name
     * @bodyParam license file nullable example: PDF or image file
     * @bodyParam tax_card file nullable example: PDF or image file
     * @bodyParam front_card_image image nullable example: image file
     * @bodyParam back_card_image image nullable example: image file
     * @bodyParam email string required example: eslam@gmail.com
     * @bodyParam phone string required example: 01000000000
     * @bodyParam country_id int required example: 1
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $user  = $this->service->update($user, $data);
        $user->refresh();
        $user->load(['city.region','country']);
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

        $user->email = now()->format('Y-m-d H:i:s') . '_' . $user->email;
        $user->phone = now()->format('Y-m-d H:i:s') . '_' . $user->phone;
        $user->save();
        $this->service->remove($user);
        return $this->respondWithSuccess(
            trans('trans.messages.account_deleted_successfully')
        );
    }

    /**
     * Get User Profile.
     * @authenticated
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $user->load(['wallet', 'country', 'city.region']);
        return $this->respondWithModel($user);
    }   
}
