<?php

namespace App\Http\Requests\Api;

use App\Enums\GenderEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\UserTypeEnum;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => fixPhone($this->phone),
        ]);
    }

    public function rules(): array
    {
        return [
            'type'                  => ['required', 'integer', 'in:' . implode(',', UserTypeEnum::values())],
            'name'                  => ['required', 'string', 'max:255'],
            'license'               => ['required', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'],
            'tax_card'              => ['required', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'],
            'front_card_image'      => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'back_card_image'       => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'email'                 => ['nullable', 'email', 'unique:users'],
            'phone'                 => ['required', 'numeric', 'unique:users', 'digits_between:10,15'],
            'country_id'            => ['required', Rule::exists('countries', 'id')->where('is_active', 1)],
        ];
    }
}
