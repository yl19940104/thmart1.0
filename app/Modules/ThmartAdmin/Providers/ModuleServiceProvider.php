<?php

namespace App\Modules\ThmartAdmin\Providers;

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
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', 'thmartAdmin');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'thmartAdmin');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'thmartAdmin');
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
