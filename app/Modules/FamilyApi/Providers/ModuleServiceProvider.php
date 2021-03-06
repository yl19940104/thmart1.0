<?php

namespace App\Modules\FamilyApi\Providers;

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
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', 'familyApi');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'familyApi');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'familyApi');
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
