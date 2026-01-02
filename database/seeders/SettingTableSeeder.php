<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [

            [ 'key' => 'name_ar', 'value' => 'اسم الشركه' ],
            [ 'key' => 'name_en', 'value' => 'Company Name' ],
            [ 'key' => 'email', 'value' => 'eslam@gmail.com' ],
            [ 'key' => 'whatsapp', 'value' => '+201000933972' ],
            [ 'key' => 'phone', 'value' => '+201000933972' ],


            [ 'key' => 'logo_ar', 'value' => 'logo_ar.png' ],
            [ 'key' => 'logo_en', 'value' => 'logo_en.png' ],
            [ 'key' => 'fav_icon', 'value' => 'fav_icon.png' ],
            [ 'key' => 'login_background', 'value' => 'login_background.png' ],
            [ 'key' => 'no_data_icon', 'value' => 'fav.png' ],
            [ 'key' => 'default_user', 'value' => 'default.png' ],


            [ 'key' => 'firebase_key', 'value' => env("FCM_SERVER_KEY") ],
            [ 'key' => 'firebase_sender_id', 'value' => '662557294717' ],
            [ 'key' => 'google_places', 'value' => 'AIzaSyAXV7nrpIKpuqyaNWNQYr3IP86_rJgcHWc' ],
        ];

        Setting::insert($data);
        Cache::forget('globalSetting');
    }

}
