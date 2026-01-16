<?php

namespace App\Http\Requests\Api;

use App\Enums\PaymentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
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
            'payment_type' => [
                'required',
                'integer',
                Rule::in(PaymentTypeEnum::values()),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_type' => __('trans.payment_type'),
        ];
    }

    public function messages(): array
    {
        return [
            'payment_type.required' => __('trans.payment_type_required'),
            'payment_type.in' => __('trans.invalid_payment_type'),
        ];
    }
}
