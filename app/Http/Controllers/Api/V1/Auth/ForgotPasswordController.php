<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Api
 * @subgroup Authentication
 */
class ForgotPasswordController extends BaseApiController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Forgot Password - Send OTP.
     *
     * Send a 4-digit OTP to the user's phone number for password reset.
     *
     * @bodyParam country_id integer required The country ID. Example: 1
     * @bodyParam phone string required The phone number. Example: 966501234567
     *
     * @response 200 {
     *   "status": 200,
     *   "data": {
     *     "message": "OTP sent successfully",
     *     "otp": "1234"
     *   }
     * }
     *
     * @response 422 {
     *   "status": 422,
     *   "message": "Phone number not found in our records."
     * }
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = $this->service->findBy('phone', $data['phone']);

            if (!$user) {
                return $this->errorWrongArgs(__('api.phone_not_found'));
            }

            $otp = generateRandomCode();

            $user->update([
                'code_expires_at' => now()->addMinutes(5),
            ]);

            DB::commit();

            return $this->respondWithArray([
                'status' => 200,
                'data' => [
                    'message' => __('api.otp_sent_successfully'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorInternalError(__('api.failed_send_otp'));
        }
    }
}
