<?php

namespace App\Providers;

use App\Models\Admin;
use App\Repositories\AbstractRepository;
use App\Repositories\AdminRepository;
use App\Repositories\Interfaces\AbstractRepositoryInterface;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\MemberRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AbstractRepositoryInterface::class, AbstractRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, function ($app) {
            return new AdminRepository(new Admin);
        });
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
