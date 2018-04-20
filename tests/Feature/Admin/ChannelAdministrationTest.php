<?php

namespace Tests\Feature\Admin;

use App\User;
use App\Channel;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;

class ChannelAdministrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_administrator_can_access_the_channel_administration_section()
    {
        $this->signInAdmin()
            ->get(route('admin.channels.index'))
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function non_administrators_cannot_access_the_channel_administration_section()
    {
        $regularUser = create(User::class);

        $this->actingAs($regularUser)
            ->get(route('admin.channels.index'))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->actingAs($regularUser)
            ->get(route('admin.channels.create'))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function an_administrator_can_create_a_channel()
    {
        $response = $this->createChannel([
            'name' => 'php',
            'description' => 'This is the channel for discussing all things PHP.',
            'color' => '#FF0000',
        ]);

        $this->get($response->headers->get('Location'))
            ->assertSee('php')
            ->assertSee('This is the channel for discussing all things PHP.');
    }

    /** @test */
    public function an_administrator_can_edit_a_channel()
    {
        $this->signInAdmin();

        $this->patch(
            route('admin.channels.update', ['channel' => create(Channel::class)]),
            $updatedChannel = [
                'name' => 'altered',
                'description' => 'altered channel description',
                'color' => '#00ff00',
                'archived' => true
            ]
        );

        $this->get(route('admin.channels.index'))
            ->assertSee($updatedChannel['name'])
            ->assertSee($updatedChannel['description']);
    }

    /** @test */
    public function an_administrator_can_archive_a_channel()
    {
        $this->signInAdmin();

        $channel = create(Channel::class);

        $this->assertFalse($channel->archived);

        $this->patch(
            route('admin.channels.update', $channel),
            [
                'name' => $channel->name,
                'description' => $channel->description,
                'color' => $channel->color,
                'archived' => true
            ]
        );

        $this->assertTrue($channel->fresh()->archived);
    }

    /** @test */
    public function the_path_to_a_thread_is_unaffected_by_its_channels_archived_status()
    {
        $thread = create(Thread::class);

        $path = $thread->path();

        $thread->channel->archive();

        $this->assertEquals($path, $thread->fresh()->path());
    }

    /** @test */
    public function an_administrator_can_edit_an_archived_channel()
    {
        $this->withoutExceptionHandling();
        $this->signInAdmin();

        $channel = create(Channel::class, ['archived' => true]);

        $this->assertTrue($channel->archived);

        $this->get(route('admin.channels.edit', $channel))
        ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function an_administrator_can_activate_an_archived_channel()
    {
        $this->signInAdmin();

        $channel = create(Channel::class, ['archived' => true]);

        $this->patch(
            route('admin.channels.update', $channel),
            [
                'name' => $channel->name,
                'description' => $channel->description,
                'color' => $channel->color,
                'archived' => false
            ]
        );

        $this->assertFalse($channel->fresh()->archived);
    }

    /** @test */
    public function a_channel_requires_a_name()
    {
        $this->createChannel(['name' => null])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function a_channel_requires_a_description()
    {
        $this->createChannel(['description' => null])
            ->assertSessionHasErrors('description');
    }

    /** @test */
    public function a_channel_requires_a_color()
    {
        $this->createChannel(['color' => null])
            ->assertSessionHasErrors('color');
    }

    protected function createChannel($overrides = [])
    {
        $this->signInAdmin();

        $channel = make(Channel::class, $overrides);

        return $this->post(route('admin.channels.store'), $channel->toArray());
    }
}
