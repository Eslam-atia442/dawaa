<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RemoveFromCartRequest extends FormRequest
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
        $userId = Auth::id();

        return [
            'cart_item_id' => [
                'required',
                'integer',
                Rule::exists('order_items', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->whereNull('order_id'); // Only cart items
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
            'cart_item_id' => __('trans.cart_item'),
            'quantity' => __('trans.quantity'),
        ];
    }

    public function messages(): array
    {
        return [
            'cart_item_id.required' => __('trans.cart_item_id_required'),
            'cart_item_id.exists' => __('trans.cart_item_not_found'),
            'quantity.min' => __('trans.quantity_min'),
        ];
    }
}
