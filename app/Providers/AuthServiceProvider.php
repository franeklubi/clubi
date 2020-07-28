<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Group' => 'App\Policies\GroupPolicy',
        'App\Post' => 'App\Policies\PostPolicy',
        'App\Comment' => 'App\Policies\CommentPolicy',
        'App\Invitation' => 'App\Policies\InvitationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
