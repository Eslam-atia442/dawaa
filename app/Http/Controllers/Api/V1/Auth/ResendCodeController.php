<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\ResendCodeRequest;
use App\Http\Resources\UserResource;
use App\Mail\User\ActivationCodeMail;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * @group Api
 * @subgroup Authentication
 */
class ResendCodeController extends BaseApiController
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
        parent::__construct($service, UserResource::class);
    }

    /**
     * Resend Code - Send New OTP.
     *
     * Send a new 4-digit OTP to the user's phone number.
     *
     * @bodyParam email string required The email address. Example: eslam@gmail.com
     *
     * @response 200 {
     *   "status": 200,
     *   "data": {
     *     "message": "Code resent successfully",
     *   }
     * }
     *
     * @response 422 {
     *   "status": 422,
     *   "message": "The email field is required."
     * }
     *
     * @param ResendCodeRequest $request
     * @return JsonResponse
     */
    public function __invoke(ResendCodeRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = $this->service->findBy('email', $data['email']);

            if (!$user) {
                return $this->errorWrongArgs(__('api.email_not_found'));
            }

            if ($user->code_expires_at > now()->addMinutes(5)) {
                return $this->errorWrongArgs(__('api.code_not_expired'));
            }

            $otp = generateRandomCode();
            Mail::to($user->email)->send(new ActivationCodeMail($user, $otp));
            $user->update([
                'code'            => $otp,
                'code_expires_at' => now()->addMinutes(5),
            ]);

            DB::commit();

            return $this->respondWithArray([
                'status' => 200,
                'data'   => [
                    'message' => __('api.code_resent_successfully'),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorInternalError(__('api.failed_resend_code'));
        }
    }
}
