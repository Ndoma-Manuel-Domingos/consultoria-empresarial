<?php

namespace App\Providers;

use App\Support\TenantManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(TenantManager::class, function () {
            return new TenantManager();
        });

        $this->app->scoped('currentTenant', function () {
            return app(TenantManager::class)->current();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();
    }
}
