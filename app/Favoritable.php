<?php

namespace App;

trait Favoritable
{
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];

        if (!$this->favorites()->where($attributes)->exists()) {
            Reputation::award($this->owner, Reputation::REPLY_FAVORITED);

            return $this->favorites()->create($attributes);
        }
    }

    public function isFavorited()
    {
        return !!$this->favorites->where('user_id', auth()->id())->count();
    }

    // Custom getter
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        Reputation::reduce($this->owner, Reputation::REPLY_FAVORITED);

        // Delete is implemented on each model, which leads to firing a model event.
        $this->favorites()->where($attributes)->get()->each->delete();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }
}
