<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Activity;
use App\Reply;
use Carbon\Carbon;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_records_activity_when_a_thread_is_created()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
        ]);

        // check out the polymorphic relation
        $activity = Activity::first();

        $this->assertEquals($activity->subject_type, get_class($thread));
    }

    /** @test */
    public function it_records_activity_when_a_reply_is_created()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $this->assertEquals(2, Activity::count());

        // check out the polymorphic relation
        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertEquals($activity->subject_type, get_class($reply));
    }

    /** @test */
    public function it_fetches_a_feed_for_any_user()
    {
        $this->signIn();

        $threads = create(Thread::class, ['user_id' => auth()->id()], 2);

        auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feed = Activity::feed(auth()->user(), 50);

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }

    /** @test */
    public function it_records_an_activity_when_a_user_favorites_a_reply()
    {
        $this->signIn();

        $reply = create(Reply::class);

        $reply->favorite();

        $this->assertEquals(3, Activity::count());
    }
}
