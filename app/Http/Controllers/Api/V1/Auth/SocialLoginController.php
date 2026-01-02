<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Api
 * @subgroup Authentication
 */
class SocialLoginController extends BaseApiController
{

    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Social Login.
     * @bodyParam social_type string required example: facebook
     * @bodyParam social_token string required example: token_from_social_provider
     * @bodyParam email string required example: user@example.com
     *
     * @param SocialLoginRequest $request
     * @return JsonResponse
     */
    public function __invoke(SocialLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->where('social_type', $data['social_type'])->where('social_token', $data['social_token'])->first();
        if (!$user) {
            return $this->errorWrongArgs(__('trans.invalid_social_credentials'));
        }

        $user->tokens()->delete();
        $user->accessToken = $user->createToken('api')->plainTextToken;

        return $this->respondWithModel($user);
    }
}
