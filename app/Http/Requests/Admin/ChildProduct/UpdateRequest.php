<?php

namespace App\Http\Requests\Admin\ChildProduct;

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
            'parent_id' => ['required', 'exists:products,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'production_line_number' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes(): array
    {
        return [
            'parent_id' => __('trans.product.index'),
            'price' => __('trans.price'),
            'quantity' => __('trans.quantity'),
            'expiry_date' => __('trans.expiry_date'),
            'production_line_number' => __('trans.production_line_number'),
            'is_active' => __('trans.is_active'),
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
