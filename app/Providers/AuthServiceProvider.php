<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Thread;
use App\Policies\ThreadPolicy;
use Illuminate\Support\Facades\Gate;
use App\Reply;
use App\Policies\ReplyPolicy;
use App\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */

    protected $policies = [
        Thread::class => ThreadPolicy::class,
        Reply::class => ReplyPolicy::class,
        User::class => UserPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // If user's email is a@a.com
        // Then he can do anything.
        // Gate::before(function ($user) {
        //     if ($user->email == 'a@a.com') {
        //         return true;
        //     }
        // });
    }
}
