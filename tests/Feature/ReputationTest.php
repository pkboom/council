<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Reply;
use App\Reputation;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_gains_points_when_they_create_a_thread()
    {
        $thread = create(Thread::class);

        $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_they_delete_a_thread()
    {
        $thread = create(Thread::class);

        $creator = $thread->creator;

        $thread->delete();

        $this->assertEquals(0, $creator->fresh()->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_they_reply_to_a_thread()
    {
        $reply = create(Reply::class);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_reply_to_a_thread_is_deleted()
    {
        $reply = create(Reply::class);

        $owner = $reply->owner;

        $reply->delete();

        $this->assertEquals(0, $owner->fresh()->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_their_reply_is_marked_as_best()
    {
        $thread = create(Thread::class);

        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $thread->markBestReply($reply);

        $this->assertEquals(Reputation::BEST_REPLY_AWARED + Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_their_reply_is_favorited()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post(route('replies.favorite', $reply->id));

        $this->assertEquals(Reputation::REPLY_FAVORITED + Reputation::REPLY_POSTED, $reply->owner->fresh()->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_reply_is_unfavorited()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post(route('replies.favorite', $reply->id));

        $this->delete(route('replies.unfavorite', $reply->id));

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->fresh()->reputation);
    }

    /** @test */
    public function when_a_thread_owner_changes_their_preferred_best_reply_the_points_should_be_transfered()
    {
        // Given a thread
        $reply = create(Reply::class);

        $thread = $reply->thread;

        $thread->markBestReply($reply);

        $this->assertEquals(Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARED, $reply->owner->reputation);

        $bestReply = create(Reply::class, ['thread_id' => $thread->id]);

        $thread->markBestReply($bestReply);

        $this->assertEquals(Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARED, $bestReply->owner->reputation);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->fresh()->reputation);
    }
}
