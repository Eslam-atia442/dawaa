<?php

namespace App\Http\Requests\Admin\Brand;

use App\Http\Requests\BaseRequest;
class UpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name.*'  => ['required', 'array'],
            'name.ar' => ['required', 'string', 'min:3', 'max:255'],
            'name.en' => ['required', 'string', 'min:3', 'max:255'],
             'image'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
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
