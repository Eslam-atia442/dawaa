<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\Admin\AdminEvent;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Notifications\GeneralNotification;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Api
 * @subgroup Authentication
 */
class LoginController extends BaseApiController
{

    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Login.
     * @bodyParam email string required example: user@example.com
     * @bodyParam password string required example: 123456
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->tokens()->delete();
            $user->accessToken = $user->createToken('api')->plainTextToken;
            return $this->respondWithModel($user);
        }
        return $this->errorWrongArgs('Wrong Credentials');
    }
}
