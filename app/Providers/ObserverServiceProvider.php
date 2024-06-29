<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\UserObsever;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Admin::observe(AdminObserver::class);
        User::observe(UserObsever::class);
    }
}
