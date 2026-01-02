<?php

namespace App\Http\Requests\Admin\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array{
        return [
            'name'                  => ['required', 'string', 'min:3', 'max:190'],
            'email'                 => [
                'required', 'string', 'email', 'min:3', 'max:190',
                Rule::unique('admins'),
            ],
            'password'              => [
                'required',
                'confirmed',
                Password::min(6)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'role_id'               => ['required', 'exists:roles,id'],
            'password_confirmation' => ['required', 'same:password'],
            'profile'               => ['nullable'],
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
