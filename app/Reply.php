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
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest', 'xp', 'path'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');

            $reply->owner->gainReputation('reply_posted');
        });

        static::deleting(function ($reply) {
            $reply->thread->decrement('replies_count');

            $reply->owner->loseReputation('reply_posted');

            if ($reply->isBest()) {
                $reply->thread->removeBestReply();
            }
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

    public function title()
    {
        return $this->thread->title;
    }

    public function wasJustPublished()
    {
        // return false; // Don't mind me replying as often as possible
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function path()
    {
        $perPage = config('council.pagination.perPage');

        $replyPosition = $this->thread->replies()->pluck('id')->search($this->id) + 1;

        $page = ceil($replyPosition / $perPage);

        return $this->thread->path() . "?page={$page}#reply-{$this->id}";
    }

    public function getPathAttribute()
    {
        return $this->path();
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = preg_replace(
            '/@([\w\-]+)/',
            '<a href="/profiles/$1">$0</a>',
            $value
        );
    }

    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    public function getXpAttribute()
    {
        $xp = config('council.reputation.reply_posted');

        if ($this->isBest()) {
            $xp += config('council.reputation.best_reply_awarded');
        }

        return $xp += $this->favorites()->count() * config('council.reputation.reply_favorited');
    }
}
