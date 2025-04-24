<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::environment("production")) {
            URL::forceScheme("https");
        }
    }
}
