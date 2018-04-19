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

    protected $points = [];

    public function setUp()
    {
        parent::setUp();

        $this->points = config('council.reputation');
    }

    /** @test */
    public function a_user_gains_points_when_they_create_a_thread()
    {
        $thread = create(Thread::class);

        $this->assertEquals($this->points['thread_published'], $thread->creator->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_they_delete_a_thread()
    {
        $thread = create(Thread::class);

        $creator = $thread->creator;

        $this->assertEquals($this->points['thread_published'], $thread->creator->reputation);

        $thread->delete();

        $this->assertEquals(0, $creator->fresh()->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_they_reply_to_a_thread()
    {
        $reply = create(Reply::class);

        $this->assertEquals($this->points['reply_posted'], $reply->owner->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_reply_to_a_thread_is_deleted()
    {
        $reply = create(Reply::class);

        $owner = $reply->owner;

        $this->assertEquals($this->points['reply_posted'], $reply->owner->reputation);

        $reply->delete();

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_their_reply_is_marked_as_best()
    {
        $thread = create(Thread::class);

        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $thread->markBestReply($reply);

        $total = $this->points['reply_posted'] + $this->points['best_reply_awarded'];
        $this->assertEquals($total, $reply->owner->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_their_reply_is_favorited()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post(route('replies.favorite', $reply));

        $this->assertEquals($this->points['reply_posted'] + $this->points['reply_favorited'], $reply->owner->fresh()->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_reply_is_unfavorited()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->post(route('replies.favorite', $reply->id));

        $this->delete(route('replies.unfavorite', $reply->id));

        $this->assertEquals($this->points['reply_posted'], $reply->owner->fresh()->reputation);
    }
}
