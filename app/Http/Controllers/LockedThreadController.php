<?php

namespace App\Http\Controllers;

use App\Thread;

class LockedThreadController extends Controller
{
    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);

        return response([], 200);
    }

    public function destory(Thread $thread)
    {
        $thread->update(['locked' => false]);

        return response([], 200);
    }
}
