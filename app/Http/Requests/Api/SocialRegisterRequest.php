<?php

namespace App\Http\Requests\Api;

use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialRegisterRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:users'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
//            'gender' => ['nullable', 'string', Rule::in(GenderEnum::values())],
//            'dob' => ['nullable', 'date', 'date_format:Y-m-d'],
//            'country_id' => ['nullable', Rule::exists('countries', 'id')->where('is_active', 1)],
        ];
    }
}
