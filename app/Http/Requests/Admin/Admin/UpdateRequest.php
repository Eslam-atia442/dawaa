<?php

namespace App\Http\Requests\Admin\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array{
        return [
            'name'                  => ['nullable', 'string', 'min:3', 'max:190'],
            'email'                 => [
                'nullable', 'string', 'email', 'min:3', 'max:190',
                Rule::unique('admins')->ignore($this->admin->id),
            ],
            'password'              => [
                'nullable',
                'confirmed',
                Password::min(6)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'role_id'               => ['nullable', 'exists:roles,id'],
            'profile'               => ['nullable'],
            'password_confirmation' => ['nullable', 'same:password'],
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes(): array{
        return [];
    }

    /**
     * @return array
     */
    public function messages(): array{
        return [];
    }
}
