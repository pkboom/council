<?php

namespace App\Http\Controllers;

use App\Reply;

class BestReplyController extends Controller
{
    public function store(Reply $reply)
    {
        // abort_if(auth()->id() !== $reply->thread->user_id);
        $this->authorize('update', $reply->thread);

        $reply->thread->markBestReply($reply);
    }
}
