<?php

namespace App;

use Laravel\Scout\Searchable;
use App\Filters\ThreadFilters;
use App\Events\ThreadReceivedNewReply;
use App\Events\ThreadWasPublished;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['path'];

    protected $casts = [
        'locked' => 'boolean',
        'pinned' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope('replyCount', function ($builder) {
        //     $builder->withCount('replies');
        // });

        // static::addGlobalScope('creator', function ($builder) {
        //     $builder->with('creator');
        // });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();

            $thread->creator->loseReputation('thread_published');
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);

            event(new ThreadWasPublished($thread));

            $thread->creator->gainReputation('thread_published');
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function bestReply()
    {
        return $this->hasOne(Reply::class, 'thread_id');
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function title()
    {
        return $this->title;
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    public function getPathAttribute()
    {
        if (!$this->channel) {
            return '';
        }

        return $this->path();
    }

    public function channel()
    {
        // return $this->belongsTo(Channel::class);
        return $this->belongsTo(Channel::class)->withoutGlobalScope('active');
    }

    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
            ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?? auth()->id())
            ->delete();
    }

    public function getIsSubscribedToAttribute()
    {
        if (!auth()->id()) {
            return false;
        }

        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function hasUpdatesFor($user)
    {
        // $key = auth()->user()->visitedThreadCacheKey($this);
        $key = $user->visitedThreadCacheKey($this);

        return cache($key) < $this->updated_at;
    }

    // This is for a bigger system
    // For the sake of testing, let it be
    public function visits()
    {
        return new Visits($this->id);
    }

    public function setSlugAttribute($value)
    {
        if (static::whereSlug($slug = str_slug($value))->exists()) {
            $slug = "{$slug}-{$this->id}";
        }

        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        if ($this->hasBestReply()) {
            Reputation::lose($this->bestReply->owner, Reputation::BEST_REPLY_AWARED);
        }

        $this->update(['best_reply_id' => $reply->id]);

        $reply->owner->gainReputation('best_reply_awarded');
    }

    public function removeBestReply()
    {
        $this->bestReply->owner->loseReputation('best_reply_awarded');

        $this->update(['best_reply_id' => null]);
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }

    public function hasBestReply()
    {
        return !is_null($this->best_reply_id);
    }

    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }
}
