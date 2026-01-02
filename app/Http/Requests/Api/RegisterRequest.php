<?php

namespace App\Http\Requests\Api;

use App\Enums\GenderEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'                  => ['required', 'string', 'min:3', 'max:255'],
            'email'                 => ['nullable', 'email', 'unique:users'],
            'phone'                 => ['required', 'numeric', 'unique:users', 'digits_between:10,15'],
            'gender'                => ['nullable', 'string', Rule::in(GenderEnum::values())],
            'dob'                   => ['nullable', 'date', 'date_format:Y-m-d'],
            'country_id'            => ['required', Rule::exists('countries', 'id')->where('is_active', 1)],
            'password'              => ['required', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }
}
