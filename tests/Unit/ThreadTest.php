<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use App\Thread;
use App\User;
use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Reply;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create(Thread::class);
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    /** @test */
    public function a_thread_has_a_creator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn()
            ->thread
            ->subscribe()
            ->addReply([
                'body' => 'Foobar',
                'user_id' => create(User::class)->id
            ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function a_thread_belongs_to_a_chennel()
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    /** @test */
    public function a_thread_has_a_path()
    {
        $this->assertEquals(
            "/threads/{$this->thread->channel->slug}/{$this->thread->slug}",
            $this->thread->path()
        );
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        // $userId is never used
        // But three months from now
        // You can guess what we pass into subscribe()
        $this->thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $this->thread->subscriptions()->where('user_id', $userId)->count()
        );

        // A notification should be prepared for the user.
        // TODO:
        // $this->assertCount(1, auth()->user()->notifications);
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        $this->thread->subscribe($userId = 1);

        $this->thread->unsubscribe($userId);

        $this->assertCount(0, $this->thread->subscriptions);
    }

    /** @test */
    public function a_thread_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $this->signIn();

        $this->assertFalse($this->thread->isSubscribedTo);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);
    }

    /** @test */
    public function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = create(Thread::class);

        tap(auth()->user(), function ($user) use ($thread) {
            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->read($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));
        });
    }

    /** @test */
    public function a_thread_records_each_visit()
    {
        $thread = make(Thread::class);

        $thread->visits()->reset();

        $this->assertSame(0, $thread->visits()->count());

        $thread->visits()->record(); // 100 to 101

        $this->assertEquals(1, $thread->visits()->count());

        $thread->visits()->record(); // 100 to 101

        $this->assertEquals(2, $thread->visits()->count());
    }

    /** @test */
    public function a_thread_body_is_sanitized_automatically()
    {
        $this->thread->update(['body' => '<script>alert("bad")</script><p>This is okay.</p>']);

        $this->assertEquals('<p>This is okay.</p>', $this->thread->body);
    }

    /** @test */
    public function a_thread_can_have_a_best_reply()
    {
        $bestReply = create(Reply::class, ['thread_id' => $this->thread->id]);

        $notBestReply = create(Reply::class, ['thread_id' => $this->thread->id]);

        $this->thread->markBestReply($bestReply);

        $this->assertTrue($this->thread->hasBestReply());

        $this->assertEquals($bestReply->id, $this->thread->bestReply->id);
    }
}
