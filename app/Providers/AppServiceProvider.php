<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Support\JmI18n;

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
        /*
         if (app()->environment(['production', 'local'])) {
             URL::forceScheme('https');
           
         }
        */

        // Share JM i18n library to all Blade views + JS.
        View::composer('*', function ($view) {
            $view->with('JM_TRANSLATIONS', JmI18n::all());
            $view->with('JM_COUNTRY', JmI18n::country());
            $view->with('JM_LOCALE', JmI18n::locale());
        });
    }
}
