<?php

namespace App\Providers;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Observers\CategoryObserve;
use App\Observers\GenreObserve;
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
        Genre::observe(GenreObserve::class);
        CastMember::observe(CastMember::class);
    }
}
