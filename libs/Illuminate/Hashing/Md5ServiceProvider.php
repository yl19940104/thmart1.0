<?php

namespace Illuminate\Hashing;

use Illuminate\Support\ServiceProvider;

class Md5ServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('md5', function () {
            return new BcryptMd5er;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['md5'];
    }
}
