<?php

namespace App\Providers;

use App\Services\Payment\UrwayService;
use App\Services\Payment\MoyasarService;
use Illuminate\Support\ServiceProvider;
use App\Services\Payment\PaymentManager;
use App\Services\Payment\MyFatoorahService;
use App\Services\Payment\Contracts\PaymentContract;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function ($app) {
            $manager = new PaymentManager();
            $manager->registerDriver('myfatoorah', new MyFatoorahService());
            $manager->registerDriver('urway', new UrwayService());
            $manager->registerDriver('moyasar', new MoyasarService());
            return $manager;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind( PaymentContract::class, PaymentManager::class);
    }
}
