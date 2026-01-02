<?php

namespace App\Http\Requests\Api;

use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

 

    public function rules(): array
    {
        $userId = auth()->id();

        return [
           'old_password' => ['required', 'string', 'min:6'],
           'new_password' => ['required', 'string', 'min:6'],
           'new_password_confirmation' => ['required', 'same:new_password'],
        ];
    }

 
}
