<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'email'
    ];

    protected $casts = [
        'confirmed' => 'boolean'
    ];

    protected $appends = [
        'isAdmin'
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function read($thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            Carbon::now()
        );
    }

    public function visitedThreadCacheKey($thread)
    {
        return sprintf('users.%s.visitis.%s', $this->id, $thread->id);
    }

    public function lastReply()
    {
        // Fetch a specific one => hasOne relationship
        // User has one relation of latest reply
        return $this->hasOne(Reply::class)->latest();
    }

    public function getAvatarPathAttribute($value)
    {
        return asset($value ? '/storage/' . $value : '/storage/avatars/default.png');
    }

    public function confirm()
    {
        $this->confirmed = true;
        $this->confirmation_token = null;

        $this->save();
    }

    public function isAdmin()
    {
        return in_array($this->email, config('council.administrators'));
    }

    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }
}
