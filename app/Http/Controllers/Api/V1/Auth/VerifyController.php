<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\VerifyRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Api
 * @subgroup Authentication
 */
class VerifyController extends BaseApiController
{

    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Verify.
     *
     * @param VerifyRequest $request
     */
    public function __invoke(VerifyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->service->findBy('phone', $data['phone']);
        if ($user->code != $data['code']) {
            return $this->errorWrongArgs('Wrong code');
        } else {
            $user->code              = null;
            $user->code_expires_at   = null;
            $user->phone_verified_at = now();
            $user->save();
        }
        return $this->respondWithModel($user);
    }
}
