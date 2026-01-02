<?php

namespace App\Http\Requests\Admin\Profile;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $admin = auth('admin')->user();
        
        return [
            'name' => ['required', 'string', 'min:3', 'max:190'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'min:3', 
                'max:190',
                Rule::unique('admins')->ignore($admin->id),
            ],
            'profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Customizing input names displayed for user
     * @return array
     */
    public function attributes() : array
    {
        return [
            'name' => trans('trans.admin.name'),
            'email' => trans('trans.admin.email'),
            'profile' => trans('trans.admin.profile'),
        ];
    }

    /**
     * @return array
     */
    public function messages() : array
    {
        return [
            'name.required' => trans('validation.required', ['attribute' => trans('trans.admin.name')]),
            'name.min' => trans('validation.min.string', ['attribute' => trans('trans.admin.name'), 'min' => 3]),
            'name.max' => trans('validation.max.string', ['attribute' => trans('trans.admin.name'), 'max' => 190]),
            'email.required' => trans('validation.required', ['attribute' => trans('trans.admin.email')]),
            'email.email' => trans('validation.email', ['attribute' => trans('trans.admin.email')]),
            'email.unique' => trans('validation.unique', ['attribute' => trans('trans.admin.email')]),
            'profile.image' => trans('validation.image', ['attribute' => trans('trans.admin.profile')]),
            'profile.mimes' => trans('validation.mimes', ['attribute' => trans('trans.admin.profile'), 'values' => 'jpeg,png,jpg,gif']),
            'profile.max' => trans('validation.max.file', ['attribute' => trans('trans.admin.profile'), 'max' => 2048]),
        ];
    }
} 