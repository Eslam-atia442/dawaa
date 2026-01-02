<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Api
 * @subgroup Authentication
 */
class RegisterController extends BaseApiController
{

    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Register.
     * @bodyParam country_id int required example: 66
     * @bodyParam phone string required example: 01000933972
     * @bodyParam password string required example: 123456
     * @bodyParam password_confirmation string required example: 123456
     * @bodyParam name string required example: eslam
     * @bodyParam email string required example: eslam@naseh.com
     * @bodyParam gender string required example: 1
     * @bodyParam device_id string required example: 123456
     * @bodyParam device_type string required example: ios or android
     * @bodyParam dob string required example: 1990-01-01
     *
     *
     *
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $data              = $request->validated();
        $data['code']      = generateRandomCode();
        $user              = $this->service->create($data);
//        $user->accessToken = $user->createToken('api')->plainTextToken;
        return $this->respondWithModel($user);
    }
}
