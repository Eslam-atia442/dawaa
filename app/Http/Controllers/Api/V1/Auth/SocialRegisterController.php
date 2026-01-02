<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\SocialRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

/**
 * @group Api
 * @subgroup Authentication
 */
class SocialRegisterController extends BaseApiController
{

    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Social Register.
     * @bodyParam social_type string required example: facebook
     * @bodyParam social_token string required example: token_from_social_provider
     * @bodyParam email string required example: user@example.com
     * @bodyParam name string required example: eslam
//     * @bodyParam gender string optional example: 1
//     * @bodyParam dob string optional example: 1990-01-01
//     * @bodyParam country_id int optional example: 66
     *
     * @param SocialRegisterRequest $request
     * @return JsonResponse
     */
    public function __invoke(SocialRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $existingUser = User::where('email', $data['email'])->where('social_type', $data['social_type'])->where('social_token', $data['social_token'])->first();

        if ($existingUser) {
            return $this->errorWrongArgs(__('trans.user_exists_social_account'));
        }

        $data = $request->validated();
        // $data['code'] = generateRandomCode();
        $data['is_active'] = true; // Social users are typically auto-activated

        $user = $this->service->create($data);
        $user->accessToken = $user->createToken('api')->plainTextToken;
        return $this->respondWithModel($user);
    }
}
