<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
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
        View::addLocation(resource_path());
        View::share(
            'setting',
            Schema::hasTable('settings') ? Setting::current() : new Setting(Setting::defaults())
        );

        Gate::before(function ($user) {
            return (int) ($user?->id ?? 0) === 1 ? true : null;
        });
    }
}
