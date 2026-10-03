<?php

namespace App\Level5\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AccessServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('view-ticket', function (?User $user) {
            return true;
        });

        Gate::define('manage-tickets', function (User $user) {
            return $user->role === 'agent' || $user->role === 'admin';
        });
    }
}
