<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation() {
        $this->merge([
            'phone' => fixPhone($this->phone),
        ]);
    }

    public function rules(): array
    {
        return [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'phone'      => ['required', 'numeric', 'exists:users,phone'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.exists' => __('api.phone_not_found'),
            'country_id.exists' => __('api.invalid_country'),
        ];
    }
} 