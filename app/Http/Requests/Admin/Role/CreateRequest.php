<?php

namespace App\Http\Requests\Admin\Role;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:190', 'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('roles'),
            ],
            'name_ar' => ['required', 'string', 'min:3', 'max:190', 'regex:/^[\p{Arabic}\s]+$/u',
                Rule::unique('roles'),
            ],
            'permissions' => 'required|array',
            'is_active' => 'sometimes|boolean',
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
