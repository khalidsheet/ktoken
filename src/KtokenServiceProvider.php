<?php

namespace Khalidmsheet\Ktoken;

use Illuminate\Support\ServiceProvider;
use Khalidmsheet\Ktoken\Commands\GenerateKtokenPassword;

class KtokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/config' => base_path('config'),
            __DIR__ . '/Middleware' => base_path('app/http/Middleware')
        ], 'ktoken');

        $this->commands([GenerateKtokenPassword::class]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
