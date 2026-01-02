<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\CheckOtpRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Api
 * @subgroup Authentication
 */
class CheckOtpController extends BaseApiController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Check OTP.
     *
     * Verify the OTP code for a user without changing any user data.
     *
     * @bodyParam country_id integer required The country ID. Example: 1
     * @bodyParam phone string required The phone number. Example: 966501234567
     * @bodyParam otp string required The 4-digit OTP. Example: 1234
     *
     * @response 200 {
     *   "status": 200,
     *   "data": {
     *     "message": "OTP verified successfully"
     *   }
     * }
     *
     * @response 422 {
     *   "status": 422,
     *   "message": "Invalid OTP or OTP has expired."
     * }
     *
     * @param CheckOtpRequest $request
     * @return JsonResponse
     */
    public function __invoke(CheckOtpRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = $this->service->findBy('phone', $data['phone']);

            if (!$user) {
                return $this->errorWrongArgs(__('api.phone_not_found'));
            }

            if ($user->code !== $data['otp']) {
                return $this->errorWrongArgs(__('api.invalid_otp'));
            }

            if (isset($user->code_expires_at) && $user->code_expires_at < now()) {
                return $this->errorWrongArgs(__('api.otp_expired'));
            }

            DB::commit();

            return $this->respondWithArray([
                'status' => 200,
                'data' => [
                    'message' => __('api.otp_verified_successfully'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorInternalError(__('api.failed_verify_otp'));
        }
    }
}
