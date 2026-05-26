<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Request as RequestModel;
use App\Models\Unit;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\RequestPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Support\Facades\Gate;
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
