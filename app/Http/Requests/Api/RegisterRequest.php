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
        $isReview = (bool) globalSetting('is_review');
        $req = $isReview ? 'nullable' : 'required';

        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users'],
            'type'                  => [$req, 'integer', 'in:' . implode(',', UserTypeEnum::values())],
            'license'               => [$req, 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'],
            'tax_card'              => [$req, 'file', 'mimes:pdf,jpeg,png,jpg,gif', 'max:10240'],
            'front_card_image'      => [$req, 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'back_card_image'       => [$req, 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'phone'                 => [$req, 'numeric', 'unique:users', 'digits_between:10,15'],
            'lat'                   => [$req, 'numeric', 'between:-90,90'],
            'long'                  => [$req, 'numeric', 'between:-180,180'],
            'map_description'       => [$req, 'string'],
            'note'                  => ['nullable', 'string'],
            'country_id'            => [$req, Rule::exists('countries', 'id')->where('is_active', 1)],
            'city_id'               => [$req, Rule::exists('cities', 'id')->where('is_active', 1)],
        ];
    }
}
