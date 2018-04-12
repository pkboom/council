<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;

class EditChannelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_admins_may_not_edit_channels()
    {
        $channel = create(Channel::class);

        $this->patch(route('admin.channels.update', $channel->slug))
            ->assertStatus(403);

        $this->signIn();

        $this->patch(route('admin.channels.update', $channel->slug))
            ->assertStatus(403);
    }

    /** @test */
    public function an_admin_can_edit_an_existing_channel()
    {
        $this->signInAdmin();

        $channel = create(Channel::class);

        $this->patch(route('admin.channels.update', $channel->slug), [
            'name' => 'somechannel',
            'description' => 'somedescriptionn',
            'archived' => true,
        ]);

        $this->assertDatabaseHas('channels', [
            'name' => 'somechannel',
            'description' => 'somedescriptionn'
        ]);
    }

    /** @test */
    public function an_admin_can_archive_a_channel()
    {
        $this->signInAdmin();

        $channel = create(Channel::class);

        $this->assertFalse($channel->archived);

        $this->patch(route('admin.channels.update', $channel->slug), [
            'name' => $channel->name,
            'description' => $channel->description,
            'archived' => true,
        ]);

        $this->assertTrue($channel->fresh()->archived);
    }
}
