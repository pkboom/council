<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Reply;

class BestReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        $replies = create(Reply::class, [], 2);

        $this->signIn($replies[1]->thread->creator);

        $this->assertFalse($replies[1]->isBest);

        $this->post(route('best-replies.store', [$replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest);
    }

    /** @test */
    public function only_the_thread_creator_may_mark_a_reply_as_best()
    {
        $reply = create(Reply::class);

        $this->signIn();

        $this->post(route('best-replies.store', [$reply->id]))->assertStatus(403);

        $this->signIn($reply->thread->creator);

        $this->post(route('best-replies.store', [$reply->id]));

        $this->assertTrue($reply->fresh()->isBest);
    }

    /** @test */
    public function if_a_best_reply_is_deleted_then_the_thread_is_properly_updated_to_reflect_that()
    {
        $reply = create(Reply::class);

        $thread = $reply->thread;

        $thread->markBestReply($reply);

        $this->assertNotNull($thread->best_reply_id);

        $reply->delete();

        $this->assertNull($thread->fresh()->best_reply_id);
    }
}
