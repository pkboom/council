<?php

namespace App\Http\Controllers;

use App\Reply;

class FavoriteController extends Controller
{
    public function store(Reply $reply)
    {
        $reply->favorite();

        $reply->owner->gainReputation('reply_favorited');
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();

        $reply->owner->loseReputation('reply_favorited');
    }
}
