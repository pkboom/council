<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable, RecordsActivity;

    protected $guarded = [];

    // Eager-load this relations for every single query
    protected $with = ['owner', 'favorites'];

    // Whenever you cast a model to an array or Json,
    // Append these custom attributes(properties) to that.
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');

            Reputation::gain($reply->owner, Reputation::REPLY_POSTED);
        });

        static::deleted(function ($reply) {
            $reply->thread->decrement('replies_count');

            Reputation::lose($reply->owner, Reputation::REPLY_POSTED);
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function path()
    {
        return $this->thread->path()."#reply-{$this->id}";
    }

    public function wasJustPublished()
    {
        // return false; // Don't mind me replying as often as possible
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function mentionedUsers()
    {
        preg_match_all('/@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = preg_replace(
            '/@([\w\-]+)/',
            '<a href="/profiles/$1">$0</a>',
            $value
        );
    }

    public function getIsBestAttribute()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }
}
