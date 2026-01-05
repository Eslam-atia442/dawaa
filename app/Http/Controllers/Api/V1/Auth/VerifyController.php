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
     * @bodyParam email string required The email address. Example: eslam@gmail.com
     * @bodyParam code string required The 4-digit OTP. Example: 1234
     *
     * @response 200 {
     *   "status": 200,
     *   "data": {
     *     "message": "OTP verified successfully"
     *   }
     * }
     *
     * @param VerifyRequest $request
     */
    public function __invoke(VerifyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->service->findBy('email', $data['email']);
        $userCode = str_pad((string)$user->code, 4, '0', STR_PAD_LEFT);
        $inputCode = str_pad((string)$data['code'], 4, '0', STR_PAD_LEFT);
        
        if ($userCode !== $inputCode) {
            return $this->errorWrongArgs(__('api.invalid_otp'));
        } else {
            $user->code              = null;
            $user->code_expires_at   = null;
            $user->email_verified_at = now();
            $user->save();
        }
        return $this->respondWithModel($user);
    }
}
