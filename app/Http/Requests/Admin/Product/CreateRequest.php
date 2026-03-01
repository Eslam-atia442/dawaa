<?php

namespace App\Http\Requests\Admin\Product;

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
            'name.*'  => ['required', 'array'],
            'name.ar' => ['required', 'string', 'min:3', 'max:255'],
            'name.en' => ['required', 'string', 'min:3', 'max:255'],
            'description.*' => ['required', 'array'],
            'description.ar' => ['required', 'string'],
            'description.en' => ['required', 'string'],
            'store_id' => ['required', 'exists:stores,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'is_active' => ['required', 'boolean'],
            'has_discount' => ['nullable', 'boolean'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:has_discount,1'],
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,svg', 'max:2048'],
            'gallery' => ['required', 'array'],
            'gallery.*' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,svg', 'max:2048'],
            'minimum_quantity' => ['required', 'integer', 'min:0'],
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
