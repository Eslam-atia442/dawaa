<?php

namespace App\Http\Requests\Admin\Setting;

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
            'name_ar'          => 'required',
            'name_en'          => 'required',
            'logo_ar'          => 'nullable',
            'logo_en'          => 'nullable',
            'logo_fr'          => 'nullable',
            'fav_icon'         => 'nullable',
            'login_background' => 'nullable',
            'no_data_icon'     => 'nullable',
            'default_user'     => 'nullable',
            'is_killed_screen' => 'nullable|boolean',
            'terms_conditions_en' => 'nullable',
            'terms_conditions_ar' => 'nullable',
            'about_us_en'         => 'nullable',
            'about_us_ar'         => 'nullable',
            'kill_screen_text_en' => 'nullable',
            'kill_screen_text_ar' => 'nullable',
            'privacy_policy_en'   => 'nullable',
            'privacy_policy_ar'   => 'nullable',
            'different_gold_price' => 'nullable',
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
