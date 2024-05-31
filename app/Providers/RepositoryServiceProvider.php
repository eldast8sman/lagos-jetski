<?php

namespace App\Providers;

use App\Repositories\AbstractRepository;
use App\Repositories\AdminRepository;
use App\Repositories\Interfaces\AbstractRepositoryInterface;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AbstractRepositoryInterface::class, AbstractRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
