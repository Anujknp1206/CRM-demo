<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Country;
use App\Models\Unit;
use App\Models\User;
use App\Models\Setting;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        User::observe(UserObserver::class);

        View::composer('*', function ($view) {

            /*
            |--------------------------------------------------------------------------
            | Demo Reset Running
            |--------------------------------------------------------------------------
            */

            if (Cache::get('demo_resetting')) {

                $view->with([
                    'settings' => null,
                    'countries' => collect(),
                    'categories' => collect(),
                    'units' => collect(),
                    'conditions' => collect(),
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Database Still Migrating?
            |--------------------------------------------------------------------------
            */

            if (
                !Schema::hasTable('setting') ||
                !Schema::hasTable('countries') ||
                !Schema::hasTable('categories') ||
                !Schema::hasTable('units') ||
                !Schema::hasTable('conditions')
            ) {

                $view->with([
                    'settings' => null,
                    'countries' => collect(),
                    'categories' => collect(),
                    'units' => collect(),
                    'conditions' => collect(),
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Everything Ready
            |--------------------------------------------------------------------------
            */

            $view->with([
                'settings' => Setting::find(1),
                'countries' => Country::all(),
                'categories' => Category::all(),
                'units' => Unit::all(),
                'conditions' => Condition::all(),
            ]);
        });
    }
}