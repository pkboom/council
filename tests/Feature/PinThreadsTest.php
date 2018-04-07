<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;

class PinThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_admins_may_not_pin_threads()
    {
        // Given a thread
        $thread = create(Thread::class);

        $this->post(route('pinned-threads.store', $thread));

        $this->assertFalse($thread->fresh()->pinned);

        $this->signIn();

        $this->post(route('pinned-threads.store', $thread));

        $this->assertFalse($thread->fresh()->pinned);
    }

    /** @test */
    public function an_admin_can_pin_threads()
    {
        $thread = create(Thread::class);

        $this->assertFalse($thread->fresh()->pinned);

        $this->signInAdmin();

        $this->post(route('pinned-threads.store', $thread));

        $this->assertTrue($thread->fresh()->pinned);
    }

    /** @test */
    public function non_admins_cannot_unpin_threads()
    {
        $thread = create(Thread::class, ['pinned' => true]);

        $this->assertTrue($thread->fresh()->pinned);

        $this->post(route('pinned-threads.destory', $thread));

        $this->assertTrue($thread->fresh()->pinned);

        $this->signIn();

        $this->assertTrue($thread->fresh()->pinned);
    }

    /** @test */
    public function an_admin_can_unlock_threads()
    {
        $thread = create(Thread::class, ['pinned' => true]);

        $this->assertTrue($thread->fresh()->pinned);

        $this->signInAdmin();

        $this->delete(route('pinned-threads.destory', $thread));

        $this->assertFalse($thread->fresh()->pinned);
    }

    /** @test */
    public function pinned_threads_should_be_at_top()
    {
        create(Thread::class, [], 2);

        $pinnedThread = create(Thread::class);

        $response = $this->getJson(route('threads'));

        $response->assertJson([
            'data' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        ]);

        $pinnedThread->update(['pinned' => true]);

        $response = $this->getJson(route('threads'));

        $response->assertJson([
            'data' => [
                ['id' => 3],
                ['id' => 1],
                ['id' => 2],
            ]
        ]);
    }
}
