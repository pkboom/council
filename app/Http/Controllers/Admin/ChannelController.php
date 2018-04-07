<?php

namespace App\Http\Controllers\Admin;

use App\Channel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ChannelController extends Controller
{
    public function index()
    {
        return view('admin.channels.index', [
            'channels' => Channel::with('threads')->get()
        ]);
    }

    public function create()
    {
        return view('admin.channels.create', [
            'channel' => new Channel
        ]);
    }

    public function edit(Channel $channel)
    {
        return view('admin.channels.edit', compact('channel'));
    }

    public function update(Channel $channel)
    {
        $data = request()->validate([
            'name' => 'required|unique:channels',
            'description' => 'required',
        ]);

        $channel->update($data);

        cache()->forget('channels');

        if (request()->wantsJson()) {
            return response($channel, 200);
        }

        return redirect(route('admin.channels.index'))
                ->with('flash', 'Your channel has been updated!');
    }

    public function store()
    {
        $data = request()->validate([
            'name' => 'required|unique:channels',
            'description' => 'required',
        ]);

        $channel = Channel::create($data + ['slug' => str_slug($data['name'])]);

        Cache::forget('channels');

        if (request()->wantsJson()) {
            return response($channel, 201);
        }

        return redirect(route('admin.channels.index'))
            ->with('flash', 'Your channel has been created!');
    }
}
