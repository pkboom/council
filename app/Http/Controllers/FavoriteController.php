<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Reputation;

class FavoriteController extends Controller
{
    public function store(Reply $reply)
    {
        $reply->favorite();

        Reputation::gain($reply->owner, Reputation::REPLY_FAVORITED);
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();

        Reputation::lose($reply->owner, Reputation::REPLY_FAVORITED);
    }
}
