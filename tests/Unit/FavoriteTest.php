<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Reply;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_favorite_a_thread()
    {
        $this->signIn();

        // Given we have a thread, a reply associated with it
        $thread = create(Thread::class);
        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        //  When we like it
        $reply->favorite();

        // Then we see it
        $this->assertCount(1, $reply->favorites);
    }
}
