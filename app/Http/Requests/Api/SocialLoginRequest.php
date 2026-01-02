<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_type' => ['required', Rule::in(['facebook', 'google', 'apple'])],
            'social_token' => ['required', 'string'],
            'email' => ['required', 'email'],
        ];
    }
}
