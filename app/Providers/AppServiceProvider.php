<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Report;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Policies\ReportPolicy;
use App\Observers\ReportObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, ClientPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);

        Report::observe(ReportObserver::class);

        Gate::before(function (User $user) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}