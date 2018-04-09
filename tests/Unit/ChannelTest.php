<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Channel;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_consists_of_threads()
    {
        $channel = create(Channel::class);
        $thread = create(Thread::class, ['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }

    /** @test */
    public function channel_can_be_archived()
    {
        $channel = create(Channel::class);

        $this->assertFalse($channel->archived);

        $channel->archive();

        $this->assertTrue($channel->archived);
    }

    /** @test */
    public function archived_channels_are_excluded_by_default()
    {
        create(Channel::class);

        create(Channel::class, ['archived' => true]);

        // $this->assertCount(1, Channel::all());
        $this->assertEquals(1, Channel::count());
    }
}
