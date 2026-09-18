<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\User;
use App\Observers\UserObserver;
use App\Card;
use App\Observers\CardObserver;
use App\Setting;
use App\Observers\SettingObserver;
use App\Department;
use App\Observers\DepartmentObserver;
use App\Position;
use App\Observers\PositionObserver;

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
        Schema::defaultStringLength(191);

        User::observe(UserObserver::class);
        Card::observe(CardObserver::class);
        Setting::observe(SettingObserver::class);
        Department::observe(DepartmentObserver::class);
        Setting::observe(SettingObserver::class);
        Position::observe(PositionObserver::class);
    }
}