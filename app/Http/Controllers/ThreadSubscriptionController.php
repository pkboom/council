<?php

namespace App\Http\Controllers;

use App\Thread;

class ThreadSubscriptionController extends Controller
{
    public function store($channel, Thread $thread)
    {
        $thread->subscribe();
    }

    public function destroy($channel, Thread $thread)
    {
        $thread->unsubscribe();
    }
}
