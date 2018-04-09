<?php

namespace App\Http\Controllers\Admin;

use App\Channel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::withArchived()->with('threads')->get();

        return view('admin.channels.index', compact('channels'));
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
            'name' => ['required', Rule::unique('users')->ignore($channel->id)],
            'description' => 'required',
            'color' => 'required',
            'archived' => 'required|boolean'
        ]);

        $channel->update($data + ['slug' => $data['name']]);

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
            'color' => 'required',
        ]);

        $channel = Channel::create($data);

        cache()->forget('channels');

        if (request()->wantsJson()) {
            return response($channel, 201);
        }

        return redirect(route('admin.channels.index'))
            ->with('flash', 'Your channel has been created!');
    }
}
