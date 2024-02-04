<?php

namespace App\Providers;

use App\Models\Redirect;
use App\Services\CrudRedirectService;
use App\Services\RedirectLogService;
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
        $this->app->bind('crudredirectservice', function () {
            return new CrudRedirectService(new Redirect);
        });

        $this->app->bind('redirectservice', function () {
            return new RedirectService(new Redirect);
        });

        $this->app->bind('redirectlogservice', function () {
            return new RedirectLogService(new Redirect);
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
