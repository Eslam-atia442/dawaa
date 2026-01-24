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
            // 'type'                  => ['required', 'integer', 'in:' . implode(',', UserTypeEnum::values())],
            'name'                  => ['required', 'string', 'max:255'],
            'license'               => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'],
            'tax_card'              => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'],
            'front_card_image'      => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'back_card_image'       => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'email'                 => ['required', 'email', 'unique:users'],
            'phone'                 => ['required', 'numeric', 'unique:users', 'digits_between:10,15'],
            'lat'                   => ['required', 'numeric', 'between:-90,90'],
            'long'                  => ['required', 'numeric', 'between:-180,180'],
            'map_description'       => ['required', 'string'],
            'note'                  => ['nullable', 'string'],
            'country_id'            => ['required', Rule::exists('countries', 'id')->where('is_active', 1)],
        ];
    }
}
