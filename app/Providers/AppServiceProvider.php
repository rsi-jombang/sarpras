<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Request as RequestModel;
use App\Models\Unit;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\RequestPolicy;
use App\Policies\UnitPolicy;
use App\Services\ItemService;
use App\Services\NumberGeneratorService;
use App\Services\PurchaseOrderService;
use App\Services\StockService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         // Services tanpa dependency — singleton agar tidak di-instantiate ulang
        $this->app->singleton(NumberGeneratorService::class);
        $this->app->singleton(StockService::class);

        // Services dengan dependency — Laravel auto-resolve via constructor injection
        // $this->app->singleton(RequestService::class);
        $this->app->singleton(PurchaseOrderService::class);
        // $this->app->singleton(StockOpnameService::class);
        $this->app->singleton(ItemService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin bypass semua policy
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        Gate::policy(RequestModel::class,  RequestPolicy::class);
        Gate::policy(Unit::class, UnitPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
    }
}
