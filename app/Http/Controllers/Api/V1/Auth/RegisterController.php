<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\User\ActivationCodeMail;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
     * 
     * @bodyParam type int required example: 1 (1: Doctor, 2: Pharmacy)
     * @bodyParam name string required example: Doctor Name or Pharmacy Name
     * @bodyParam license file required example: PDF or image file
     * @bodyParam tax_card file required example: PDF or image file
     * @bodyParam front_card_image image required example: image file
     * @bodyParam back_card_image image required example: image file
     * @bodyParam email string nullable example: eslam@gmail.com
     * @bodyParam phone string required example: 01000000000
     * @bodyParam country_id int required example: 1
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $data                      = $request->validated();
        $data['code']              = generateRandomCode();
        $data['code_expires_at']   = now()->addMinutes(5);
        $user                      = $this->service->create($data);

        if ($user->email) {
            Mail::to($user->email)->send(new ActivationCodeMail($user, $data['code']));
        }

        return $this->respondWithModel($user);
    }
}
