<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    'App\Models\User' => 'App\Policies\UserPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->isAdministrator()) {
                return true;
            }
        });

        // Gate::define('update-user', function(User $userAutheticated, User $userToUpdate) {
        //     // ddd([$userAutheticated, $userToUpdate]);
        //     return $userAutheticated->id === $userToUpdate->id;
        // });

        // Gate::define('view-user', function(User $userAutheticated, User $userToView) {
        //     // ddd([$userAutheticated, $userToUpdate]);
        //     return $userAutheticated->id === $userToView->id;
        // });
    }
}
