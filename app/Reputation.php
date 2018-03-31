<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reputation extends Model
{
    const THREAD_WAS_PUBLISHED = 10;
    const REPLY_POSTED = 2;
    const BEST_REPLY_AWARED = 50;
    const REPLY_FAVORITED = 5;

    public static function gain($user, $points)
    {
        $user->increment('reputation', $points);
    }

    public static function lose($user, $points)
    {
        $user->decrement('reputation', $points);
    }
}
