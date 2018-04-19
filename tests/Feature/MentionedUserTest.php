<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Reply;
use App\Thread;

class MentionedUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $john = create(User::class, ['username' => 'JohnDoe']);

        $this->actingAs($john);

        $jane = create(User::class, ['username' => 'JaneDoe']);

        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'thread_id' => $thread->id,
            'body' => 'Hey @JaneDoe check this out'
        ]);

        $this->postJson($thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function it_can_fetch_all_metioned_users_starting_with_the_given_characters()
    {
        $this->withoutExceptionHandling();

        create(User::class, ['name' => 'john1']);
        create(User::class, ['name' => 'john2']);
        create(User::class, ['name' => 'jane']);

        $response = $this->json('GET', '/api/users', ['name' => 'john'])->json();

        $this->assertCount(2, $response);
    }
}
