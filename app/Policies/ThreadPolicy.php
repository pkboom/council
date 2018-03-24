<?php

namespace App\Policies;

use App\User;
use App\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    // https://laravel.com/docs/5.6/authorization#policy-filters
    // public function before($user, $ability)
    // {
    //     if ($user->email == 'a@a.com') {
    //         return true;
    //     }
    // }

    public function update(User $user, Thread $thread)
    {
        return $thread->user_id == $user->id;
    }
}
