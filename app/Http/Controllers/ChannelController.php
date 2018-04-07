<?php

namespace App\Http\Controllers;

use App\Channel;

class ChannelController extends Controller
{
    public function show()
    {
        return Channel::get();
    }
}
