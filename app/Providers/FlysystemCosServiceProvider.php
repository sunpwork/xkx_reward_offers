<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Cos\CosAdapter;

class FlysystemCosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Filesystem::class, function ($app) {
            $adapter = new CosAdapter(config('flysystem-cos'));
            return new Filesystem($adapter);
        });
        $this->app->alias(Filesystem::class,'flysystem');
    }
}
