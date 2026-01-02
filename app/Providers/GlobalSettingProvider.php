<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class GlobalSettingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {

        if (Schema::hasTable('settings')) {
            $globalSetting = Cache::get('globalSetting');
             if (!$globalSetting) {

                return Cache::rememberForever('globalSetting', function () {
                    $seeting = new Setting();
                     $seeting = $seeting->get();
                     return $seeting ?? null;
                });
            }
        }

    }
}
