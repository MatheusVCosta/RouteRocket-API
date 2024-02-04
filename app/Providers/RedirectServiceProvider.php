<?php

namespace App\Providers;

use App\Models\Redirect;
use App\Services\CrudRedirectService;
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
