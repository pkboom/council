<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Reply;

class ParticipateInThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->post('threads/php/1/replies')
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class);

        $reply = make(Reply::class);
        $this->post($thread->path() . '/replies', $reply->toArray());

        // We can't see that
        // Because we load replies on the client-side, not server-side,
        // In other words, replies are now being loaded with javascript, not php.
        // So phpunit can't test it.
        // $this->get($thread->path())
        //     ->assertSee($reply->body);

        $this->assertDatabaseHas('replies', ['body' => $reply->body, ]);

        // We created a thread and save to $thread.
        // But we changed data behind the scene.
        // $this->assertEquals(1, $thread->replies_count);
        // Give me fresh data and check replies.
        $this->assertEquals(1, $thread->fresh()->replies->count());
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $reply = make(Reply::class, ['body' => null]);

        $this->postJson($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
    public function an_authorized_user_can_delete_a_reply()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);
        $this->delete("/replies/{$reply->id}")
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, $reply->thread->fresh()->replies->count());
    }

    /** @test */
    public function an_unauthorized_user_may_not_delete_a_reply()
    {
        $reply = create(Reply::class);

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $updatedReply = 'You been changed, fool.';
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply
        ]);
    }

    /** @test */
    public function unauthorized_users_may_not_update_a_reply()
    {
        $reply = create(Reply::class);

        $this->patch("/replies/{$reply->id}", ['body' => 'new reply'])
            ->assertRedirect('login');

        $this->signIn();

        // 403: Forbidden
        $this->patch("/replies/{$reply->id}", ['body' => 'new reply'])
            ->assertStatus(403);
    }

    /** @test */
    public function replies_that_contain_spam_may_not_be_created()
    {
        $this->signIn();

        $thread = create(Thread::class);
        $reply = make(Reply::class, [
            'body' => 'Yahoo Customer Support'
        ]);

        // ValidationException occurs through CreatePostRequest
        // Handler::render processes this exception.
        // 422: Unprocessable Entity
        $this->postJson($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
    public function users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $reply = make(Reply::class);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(200);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
