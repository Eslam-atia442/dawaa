<?php

namespace App\Http\Requests\Api;

use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => fixPhone($this->phone),
            ]);
        }
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name'        => ['sometimes', 'string', 'min:3', 'max:255'],
            'email'       => ['nullable', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'phone'       => ['sometimes', 'numeric', Rule::unique('users', 'phone')->ignore($userId)],
            'gender'      => ['sometimes', 'string', Rule::in(GenderEnum::values())],
            'dob'         => ['sometimes', 'date', 'date_format:Y-m-d'],
            'country_id'  => ['sometimes', Rule::exists('countries', 'id')->where('is_active', 1)],
            'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'       => trans('trans.admin.name'),
            'email'      => trans('trans.admin.email'),
            'phone'      => trans('trans.admin.phone'),
            'gender'     => trans('trans.admin.gender'),
            'dob'        => trans('trans.admin.dob'),
            'country_id' => trans('trans.admin.country'),
            'avatar'     => trans('trans.admin.profile'),
        ];
    }
}
