<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(function ($query) {
                    $query->whereNotNull('parent_id') // Only parent products
                        ->where('is_active', 1);
                }),
            ],
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => __('trans.product.index'),
            'child_product_id' => __('trans.child-product.index'),
            'quantity' => __('trans.quantity'),
            'note' => __('trans.note'),
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => __('trans.product_id_required'),
            'product_id.exists' => __('trans.product_not_found'),
            'child_product_id.required' => __('trans.child_product_id_required'),
            'child_product_id.exists' => __('trans.child_product_not_found'),
            'quantity.min' => __('trans.quantity_min'),
            'note.max' => __('trans.note_max'),
        ];
    }
}
