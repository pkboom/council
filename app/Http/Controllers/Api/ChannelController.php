<?php

namespace App\Http\Controllers\Api;

use App\Channel;
use App\Http\Controllers\Controller;

class ChannelController extends Controller
{
    public function show()
    {
        // dd(cache()->get('channels', 'none'));
        // dd(Channel::all());

        return cache()->rememberForever('channels', function () {
            return Channel::all();
        });
    }
}
