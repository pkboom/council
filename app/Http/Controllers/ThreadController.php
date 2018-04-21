<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use App\Trending;
use App\Rules\SpamFree;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;
use Illuminate\Validation\Rule;

class ThreadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Channel $channel, ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads,
            'channel' => $channel,
        ]);
    }

    public function show($channel, Thread $thread, Trending $trending)
    {
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        // $thread->visits()->record();
        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    public function store(Recaptcha $recaptcha)
    {
        $data = request()->validate([
            'title' => ['required', new SpamFree],
            'body' => ['required', new SpamFree],
            'channel_id' => [
                'required',
                Rule::exists('channels', 'id')->where(function ($query) {
                    $query->where('archived', false);
                }),
            ],
            'g-recaptcha-response' => ['required', $recaptcha],
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
        ]);

        if (request()->wantsJson()) {
            return response($thread, 201);
        }

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published');
    }

    public function create()
    {
        return view('threads.create', [
            'channels' => Channel::all()
        ]);
    }

    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        // $thread->replies()->delete();
        // == static::deleting in Thread.php

        $thread->delete();

        if (request()->wantsJson()) {
            // return response()->json([], 204); // the same as below
            return response([], 204);
        }

        return redirect('/threads');
    }

    public function update($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $data = request()->validate([
            'title' => ['required', new SpamFree],
            'body' => ['required', new SpamFree],
            ]);

        $thread->update($data);

        return $thread;
    }

    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::orderBy('pinned', 'desc')
            ->latest()
            ->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(25);
    }
}
