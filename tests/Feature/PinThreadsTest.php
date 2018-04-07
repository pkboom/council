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
    public function an_admin_can_unpin_threads()
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
        $threads = create(Thread::class, [], 3);

        $ids = $threads->pluck('id');

        $this->getJson(route('threads'))->assertJson([
            'data' => [
                ['id' => $ids[0]],
                ['id' => $ids[1]],
                ['id' => $ids[2]],
            ]
        ]);

        $pinned = tap($threads->last())->update(['pinned' => true]);

        $this->getJson(route('threads'))->assertJson([
            'data' => [
                ['id' => $pinned->id],
                ['id' => $ids[0]],
                ['id' => $ids[1]],
            ]
        ]);
    }
}
