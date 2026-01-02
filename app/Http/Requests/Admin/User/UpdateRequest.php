<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateRequest extends BaseRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users','email')->ignore($this->user->id)],
            'phone' => ['required', 'string', 'max:255'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(6)->mixedCase()->letters()->numbers()->symbols(),
            ],
            'password_confirmation' => ['nullable', 'same:password'],
            'country_id' => ['required', 'exists:countries,id'],
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
