<?php

namespace App\Http\Requests\Admin\Country;

use App\Http\Requests\BaseRequest;

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
            'name.*'        => 'required|max:191',
            'name.ar'       => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\p{Arabic}\s]+$/u'],
            'name.en'       => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'currency.*'    => 'required|max:191',
            'key'           => 'required|unique:countries,key',
            'currency_code' => 'required|unique:countries,currency_code',
            'iso2'          => 'required',

        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
