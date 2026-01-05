<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\UserTypeEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class CreateRequest extends BaseRequest
{



    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => fixPhone($this->phone),
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'integer', 'in:' . implode(',', UserTypeEnum::values())],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:255', 'unique:users'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(6)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'password_confirmation' => ['nullable', 'same:password'],
            'license' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'tax_card' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'front_card_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'back_card_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes() : array
    {
        return [];
    }

    /**
     * @return array
     */
    public function messages() : array
    {
        return [];
    }
}
