<?php

namespace App\Http\Requests\Admin\Product;

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
            'description.*' => ['required', 'array'],
            'description.ar' => ['required', 'string'],
            'description.en' => ['required', 'string'],
            'store_id' => ['required', 'exists:stores,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'is_active' => ['required', 'boolean'],

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
