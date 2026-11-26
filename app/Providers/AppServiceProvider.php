<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        // Protection against N+1 problems: on your computer and in the tests, Laravel throws an
        // error when a relationship is loaded inside a loop instead of up front with with().
        // In production the page keeps working, it is only slower.
        Model::preventLazyLoading(! $this->app->isProduction());

        // Some pages load only a few columns, like with('user:id,name'). This throws an error
        // when a view uses a column that was not loaded, instead of silently showing nothing.
        Model::preventAccessingMissingAttributes(! $this->app->isProduction());
    }
}
