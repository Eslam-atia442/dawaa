<?php

namespace App\Http\Requests\Admin\Profile;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'min:6'],
            'new_password' => [
                'required',
                'string',
                'min:6',
                'different:old_password',
//                Password::min(6)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes() : array
    {
        return [
            'old_password' => trans('trans.auth.old_password'),
            'new_password' => trans('trans.auth.new_password'),
            'new_password_confirmation' => trans('trans.auth.new_password_confirmation'),
        ];
    }

    /**
     * @return array
     */
    public function messages() : array
    {
        return [
            'old_password.required' => trans('validation.required', ['attribute' => trans('trans.auth.old_password')]),
            'old_password.min' => trans('validation.min.string', ['attribute' => trans('trans.auth.old_password'), 'min' => 6]),
            'new_password.required' => trans('validation.required', ['attribute' => trans('trans.auth.new_password')]),
            'new_password.min' => trans('validation.min.string', ['attribute' => trans('trans.auth.new_password'), 'min' => 6]),
            'new_password.different' => trans('validation.different', ['attribute' => trans('trans.auth.new_password'), 'other' => trans('trans.auth.old_password')]),
            'new_password_confirmation.required' => trans('validation.required', ['attribute' => trans('trans.auth.new_password_confirmation')]),
            'new_password_confirmation.same' => trans('validation.same', ['attribute' => trans('trans.auth.new_password_confirmation'), 'other' => trans('trans.auth.new_password')]),
        ];
    }
}
