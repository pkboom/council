<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Thread;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_a_file()
    {
        $user = create(User::class);

        $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
    }

    /** @test */
    public function profiles_display_all_threads_created_by_the_associated_user()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->get(route('profile', auth()->user()->name))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
