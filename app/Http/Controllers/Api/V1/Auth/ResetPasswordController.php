<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Api
 * @subgroup Authentication
 */
class ResetPasswordController extends BaseApiController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Reset Password - Verify OTP and Reset Password.
     *
     * Verify the OTP and reset the user's password.
     *
     * @bodyParam country_id integer required The country ID. Example: 1
     * @bodyParam phone string required The phone number. Example: 966501234567
     * @bodyParam otp string required The 4-digit OTP. Example: 1234
     * @bodyParam password string required The new password (min 8 characters). Example: newpassword123
     * @bodyParam password_confirmation string required Password confirmation. Example: newpassword123
     *
     * @response 200 {
     *   "status": 200,
     *   "data": {
     *     "message": "Password reset successfully"
     *   }
     * }
     *
     * @response 422 {
     *   "status": 422,
     *   "message": "Invalid OTP or OTP has expired."
     * }
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = $this->service->findBy('phone', $data['phone'] );

            if (!$user) {
                return $this->errorWrongArgs(__('api.phone_not_found'));
            }

            // Convert both to string and pad with leading zeros for comparison
            $userCode = str_pad((string)$user->code, 4, '0', STR_PAD_LEFT);
            $inputCode = str_pad((string)$data['otp'], 4, '0', STR_PAD_LEFT);
            
            if ($userCode !== $inputCode) {
                return $this->errorWrongArgs(__('api.invalid_otp'));
            }

            if (isset($user->code_expires_at) && $user->code_expires_at < now()) {
                return $this->errorWrongArgs(__('api.otp_expired'));
            }

            $user->update([
                'password' => $data['password'],
                'code' => null,
                'code_expires_at' => null,
            ]);

            DB::commit();

            return $this->respondWithArray([
                'status' => 200,
                'data' => [
                    'message' => __('api.password_reset_successfully')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorInternalError(__('api.failed_reset_password'));
        }
    }
}
