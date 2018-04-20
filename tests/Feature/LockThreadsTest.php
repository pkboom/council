<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Reply;
use App\User;

class LockThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_admins_may_not_lock_threads()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function an_admin_can_lock_a_thread()
    {
        $admin = create(User::class);

        config(['council.administrators' => [$admin->email]]);

        $this->actingAs($admin);

        $thread = create(Thread::class);

        $this->post(route('locked-threads.store', $thread));

        $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /** @test */
    public function non_admins_may_not_unlock_a_thread()
    {
        $thread = create(Thread::class, ['locked' => true]);

        $this->assertTrue($thread->fresh()->locked);

        $this->delete(route('locked-threads.destory', $thread));

        $this->assertTrue($thread->fresh()->locked);

        $this->signIn();

        $this->delete(route('locked-threads.destory', $thread));

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function an_admin_can_unlock_a_thread()
    {
        $admin = create(User::class);

        config(['council.administrators' => [$admin->email]]);

        $this->actingAs($admin);

        $thread = create(Thread::class, ['locked' => true]);

        $this->assertTrue($thread->locked);

        $this->delete(route('locked-threads.destory', $thread));

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread = create(Thread::class, ['locked' => true]);

        $reply = make(Reply::class, ['thread_id' => $thread->id]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }
}
