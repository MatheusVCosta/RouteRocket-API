<?php

namespace App\Providers;

use App\Services\RedirectService;
use Illuminate\Support\ServiceProvider;

class RedirectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('redirectservice', function () {
            return new RedirectService();
        });
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
