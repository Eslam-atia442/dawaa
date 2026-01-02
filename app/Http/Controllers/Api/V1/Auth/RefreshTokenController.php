<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Controllers\BaseApiController;
use App\Services\UserService;


/**
 * @group Api
 * @subgroup Authentication
 */
class RefreshTokenController extends BaseApiController
{

    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }


    /**
     * Refresh Token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $user->accessToken = $user->createToken('api')->plainTextToken;
        
          return $this->respondWithModel($user);

    }
}
