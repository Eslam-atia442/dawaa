<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResendCodeRequest extends FormRequest
{
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
            'phone'      => ['required', 'numeric', 'exists:users,phone' ,'digits_between:10,15'],
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
