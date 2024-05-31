<?php

namespace App\Providers;

use App\Events\AdminRegistered;
use App\Listeners\SendAdminActivationMail;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected $listen = [
        AdminRegistered::class => [
            SendAdminActivationMail::class,
        ],
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
