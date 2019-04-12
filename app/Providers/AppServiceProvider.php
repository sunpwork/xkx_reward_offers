<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\ApplyRecord::observe(\App\Observers\ApplyRecordObserver::class);
        \App\Models\RealNameAuthImage::observe(\App\Observers\RealNameAuthImageObserver::class);
        \App\Models\Errand::observe(\App\Observers\ErrandObserver::class);

        \Carbon\Carbon::setLocale('zh');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
