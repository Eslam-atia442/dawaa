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
 
    public function rules(): array
    {
        return [
            'email'      => ['required', 'email', 'exists:users,email'],
            'code'       => ['required', 'numeric'],
        ];
    }
}
