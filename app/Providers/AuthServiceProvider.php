<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('edit-delete-appointment', function ($user, $branch, $appointment){
            return $branch->id === $appointment->branch_id;
        });

        Gate::define('edit-delete-camp', function ($user, $branch, $camp){
            return $branch->id === $camp->branch_id;
        });
    }
}
