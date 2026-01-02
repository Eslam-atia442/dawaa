<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
            'phone'      => ['required', 'numeric', 'exists:users,phone','digits_between:10,15'],
            'otp'        => ['required', 'numeric', 'digits:4'],
            'password'   => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.exists' => __('api.phone_not_found'),
            'country_id.exists' => __('api.invalid_country'),
            'otp.digits' => __('api.invalid_otp_format'),
            'password.min' => __('api.password_min_length'),
            'password_confirmation.same' => __('api.password_confirmation_mismatch'),
        ];
    }
}
