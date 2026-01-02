<?php

use App\Enums\NotificationEnum;
use App\Models\Setting;
use Illuminate\Support\Facades\App;

use App\Models\Seo;
use Illuminate\Support\Facades\Cache;

function seo($key)
{
    return Seo::where('key', $key)->first();
}

function convert2english($string)
{
    $newNumbers = range(0, 9);
    $arabic     = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    $string     = str_replace($arabic, $newNumbers, $string);
    return $string;
}

function fixPhone($string = null)
{
    if (!$string) {
        return null;
    }

    $result = convert2english($string);
    $result = ltrim($result, '00');
    $result = ltrim($result, '0');
    $result = ltrim($result, '+');
    return $result;
}

function getYoutubeVideoId($youtubeUrl)
{
    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",
        $youtubeUrl, $videoId);
    return $youtubeVideoId = isset($videoId[1]) ? $videoId[1] : "";
}

function lang()
{
    return App()->getLocale();
}

function generateRandomCode()
{
    return str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
}

if (!function_exists('languages')) {
    function languages()
    {
        return ['ar', 'en'];
    }
}

if (!function_exists('defaultLang')) {
    function defaultLang()
    {
        return 'ar';
    }
}

if (!function_exists('NewNumberFormat')) {
    function NewNumberFormat($number)
    {
        $formatter       = new NumberFormatter('en', NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
        $formattedNumber = $formatter->format($number);
        return $formattedNumber;
    }
}


if (!function_exists('globalSetting')) {
    function globalSetting($key)
    {

        $setting_model = new Setting();
        try {
            $globalSetting = Cache::get('globalSetting');

            if (in_array($key, $setting_model->filesToUpload)) {
                $settingForKey = $globalSetting->where('key', $key)->first();
                return $settingForKey->getMedia($key);
            }

            $setting = $globalSetting->where('key', $key)->first();
            return $setting ? $setting->value : null;
        } catch (\Exception $e) {
            return null;
        }
    }


}
if (!function_exists('adminNotificationLink')) {
    function adminNotificationLink($data)
    {

        switch ($data['type']) {
            case NotificationEnum::user_login->value && $data['model'] == 'User':
                return route('admin.users.show', $data['model_id']);
                break;
            default:
                return route('home');
        }
    }


}





