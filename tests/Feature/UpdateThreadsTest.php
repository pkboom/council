<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function unauthorized_users_may_not_update_threads()
    {
        $thread = create(Thread::class);

        $this->patch($thread->path(), [])->assertStatus(403);
    }

    /** @test */
    public function a_thread_requires_a_title_and_body_to_be_updated()
    {
        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->patch($thread->path(), ['body' => 'changed'])
            ->assertSessionHasErrors('title');

        $this->patch($thread->path(), ['title' => 'changed'])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_can_be_updated_by_its_creator()
    {
        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->patch($thread->path(), ['title' => 'new title', 'body' => 'new body']);

        $this->assertDatabaseHas('threads', [
            'title' => 'new title',
            'body' => 'new body'
        ]);
    }
}
