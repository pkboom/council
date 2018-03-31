<?php

namespace App\Providers;

use App\User;
use App\Reply;
use App\Thread;
use App\Policies\UserPolicy;
use App\Policies\ReplyPolicy;
use App\Policies\ThreadPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
