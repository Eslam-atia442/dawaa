<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Resources\UserResource;
use App\Mail\User\PasswordResetMail;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
     * Forgot Password - Send Reset Code.
     *
     * Send a password reset code to the user's email address.
     *
     * @bodyParam email string required The email address. Example: user@example.com
     *
     * @response 200 {
     *   "status": 200,
     *   "data": {
     *     "message": "Password reset code sent successfully"
     *   }
     * }
     *
     * @response 422 {
     *   "status": 422,
     *   "message": "Email not found in our records."
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

            $user = $this->service->findBy('email', $data['email']);

            if (!$user) {
                return $this->errorWrongArgs(__('api.email_not_found'));
            }

                $password = Str::random(12);
            $user->update([
                'password' => $password,
            ]);
            Mail::to($user->email)->send(new PasswordResetMail($user, $password));
            DB::commit();

            
            return $this->respondWithArray([
                'success' => true,
                'message' => __('trans.password_reset_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorInternalError(__('api.failed_send_email'));
        }
    }
}
