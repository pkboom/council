<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasReputation;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'email'
    ];

    protected $casts = [
        'confirmed' => 'boolean',
        'reputation' => 'integer'
    ];

    protected $appends = [
        'isAdmin'
    ];

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function confirm()
    {
        $this->confirmed = true;
        $this->confirmation_token = null;

        $this->save();
    }

    public function isAdmin()
    {
        return in_array(
            strtolower($this->email),
            array_map('strtolower', config('council.administrators'))
        );
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

    public function getAvatarPathAttribute($value)
    {
        return asset($value ? '/storage/' . $value : '/images/avatars/default.svg');
    }

    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }
}
