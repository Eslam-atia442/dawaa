<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation() {
        $this->merge([
            'phone' => fixPhone($this->phone),
        ]);
    }
    public function rules(): array
    {
        return [
            'country_id' => ['required', 'exists:countries,id'],
            'phone'      => ['required', 'numeric', 'exists:users,phone'],
            'code'       => ['required', 'numeric'],
        ];
    }
}
