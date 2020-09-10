<?php

namespace App\Providers;

use App\Models\Category;
use App\Observers\CategoryObserve;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::addExtension('html', 'blade');

        Category::observe(CategoryObserve::class);
    }
}
