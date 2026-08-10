<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        FilamentShield::enforcePolicies();
    }
}
