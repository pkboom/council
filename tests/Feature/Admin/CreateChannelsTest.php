<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Channel;

class CreateChannelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_admins_can_not_create_channels()
    {
        $user = create(User::class);

        $this->post(route('admin.channels.store'))->assertStatus(403);

        $this->actingAs($user)
            ->post(route('admin.channels.store'))
            ->assertStatus(403);
    }

    /** @test */
    public function an_admin_can_create_channels()
    {
        $this->signInAdmin();

        $this->post(route('admin.channels.store'), [
            'name' => 'new channel',
            'description' => 'new description'
        ]);

        $this->assertDatabaseHas('channels', [
           'name' => 'new channel'
        ]);
    }

    /** @test */
    public function a_channel_requires_a_name()
    {
        $this->createChannel(['name' => null])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_channel_must_be_unique()
    {
        create(Channel::class, ['name' => 'php']);

        $this->createChannel(['name' => 'php'])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_channel_requires_a_description()
    {
        $this->createChannel(['description' => null])
            ->assertSessionHasErrors('description');
    }

    public function createChannel($overrides = [])
    {
        $this->signInAdmin();

        $channel = make(Channel::class, $overrides);

        return $this->post(route('admin.channels.store'), $channel->toArray());
    }
}
