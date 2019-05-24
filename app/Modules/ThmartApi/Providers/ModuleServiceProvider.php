<?php

namespace App\Modules\ThmartApi\Providers;

use Caffeinated\Modules\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', 'thmartApi');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'thmartApi');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'thmartApi');
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
