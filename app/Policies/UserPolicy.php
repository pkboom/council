<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $signedUser, User $user)
    {
        return $signedUser->id == $user->id;
    }
}
