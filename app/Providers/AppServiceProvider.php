<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationActivity;
use App\Models\Guru;
use App\Observers\GuruObserver;
use App\Policies\RolePolicy;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

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
        Gate::policy(Role::class, RolePolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        URL::resolveMissingNamedRoutesUsing(function (string $name, array $parameters = [], bool $absolute = true): ?string {
            if ($name === 'login') {
                return route('school.login', $parameters, $absolute);
            }

            return null;
        });

        Guru::observe(GuruObserver::class);

        Event::listen(Login::class, [LogAuthenticationActivity::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthenticationActivity::class, 'handleLogout']);
        Event::listen(Failed::class, [LogAuthenticationActivity::class, 'handleFailed']);
    }
}
