<?php

namespace App\Http\Controllers;

use App\Reply;

class FavoriteController extends Controller
{
    public function store(Reply $reply)
    {
        $reply->favorite();

        return back();
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}
