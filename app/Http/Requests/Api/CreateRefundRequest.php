<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRefundRequest extends FormRequest
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
        // dd($this->all());
        return [
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id',
                Rule::exists('orders', 'id')->where(function ($query) {
                    $query->where('user_id', auth('sanctum')->id());
                }),
            ],
            'refund_type' => [
                'required',
                'string',
                Rule::in(array_keys(config('refund.types', []))),
            ],
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.order_item_id' => [
                'required',
                // 'numeric',
                'exists:order_items,id',
            ],
            'items.*.quantity' => [
                'required',
                // 'integer',
                'min:1',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => __('trans.order_id'),
            'refund_type' => __('trans.refund_type'),
            'items' => __('trans.refund_items'),
            'items.*.order_item_id' => __('trans.order_item_id'),
            'items.*.quantity' => __('trans.quantity'),
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => __('trans.order_id_required'),
            'order_id.exists' => __('trans.order_not_found'),
            'refund_type.required' => __('trans.refund_type_required'),
            'refund_type.in' => __('trans.invalid_refund_type'),
            'items.required' => __('trans.refund_items_required'),
            'items.array' => __('trans.refund_items_must_be_array'),
            'items.min' => __('trans.at_least_one_item_required'),
            'items.*.order_item_id.required' => __('trans.order_item_id_required'),
            'items.*.order_item_id.exists' => __('trans.order_item_not_found'),
            'items.*.quantity.required' => __('trans.quantity_required'),
            'items.*.quantity.integer' => __('trans.quantity_must_be_integer'),
            'items.*.quantity.min' => __('trans.quantity_must_be_positive'),
        ];
    }
}