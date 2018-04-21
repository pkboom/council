<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];

    protected $appends = ['favoritedModel'];

    public function subject()
    {
        return $this->morphTo();
    }

    public function getFavoritedModelAttribute()
    {
        $favoritedModel = null;

        if ($this->subject_type === Favorite::class) {
            $subject = $this->subject()->firstOrFail();

            if ($subject->favorited_type == Reply::class) {
                $favoritedModel = Reply::find($subject->favorited_id);
            }
        }

        return $favoritedModel;
    }

    public static function feed($user)
    {
        return static::where('user_id', $user->id)
            ->latest()
            ->with('subject')
            ->paginate(30);
    }
}
