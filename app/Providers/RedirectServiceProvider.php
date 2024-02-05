<?php

namespace App\Providers;

use App\Models\Redirect;
use App\Models\RedirectLog;
use App\Services\RedirectService;
use App\Services\RedirectLogService;
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
            return new RedirectService(new Redirect);
        });

        $this->app->bind('redirectlogservice', function () {
            return new RedirectLogService(new RedirectLog);
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
