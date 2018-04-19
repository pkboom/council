<?php

namespace App\Http\Controllers;

use Gate;
use App\Reply;
use App\Thread;
use App\Rules\SpamFree;
use Illuminate\Http\Request;
use App\Http\Requests\CreatePostRequest;

class ReplyController extends Controller
{
    public function index($channelId, Thread $thread)
    {
        return $thread->paginate(config('council.pagination.perPage'));
    }

    public function store($channelId, Thread $thread, CreatePostRequest $form)
    {
        // if (Gate::denies('create', new Reply)) {
        //     return response(
        //         'Your are posting too frequently. Please take a break.',
        //         422
        //     );
        // }
        // https://laravel.com/docs/5.6/authorization#via-the-user-model
        // == $this->authorize('create', new Reply);

        if ($thread->locked) {
            return response('Thread is locked', 422);
        }

        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        request()->validate(['body' => ['required', new SpamFree]]);

        $reply->update(request(['body']));
    }
}
