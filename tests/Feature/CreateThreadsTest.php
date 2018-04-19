<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Channel;
use App\Reply;
use App\User;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use App\Rules\Recaptcha;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    public function setUp()
    {
        parent::setUp();

        // For tests, Recaptch validation is going to be true
        // app->singleton for Recaptcha would be a mock
        app()->singleton(Recaptcha::class, function () {
            // Mockery, mock Recaptcha
            // You should receive a call to 'passes' and return true
            return \Mockery::mock(Recaptcha::class, function ($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });

        // the same
        // $mock = \Mockery::mock(Recaptcha::class);
        // $mock->shouldReceive('passes')->andReturn(true);
        // app()->singleton(Recaptcha::class, $mock);
    }

    /** @test */
    public function a_user_can_create_new_forum_threads()
    {
        $response = $this->publishThread(['title' => 'some title', 'body' => 'some body']);

        $this->get($response->headers->get('Location'))
            ->assertSee('some title')
            ->assertSee('some body');
    }

    /** @test */
    public function guests_may_not_create_threads()
    {
        $this->get('/threads/create')
            ->assertRedirect(route('login'));

        $this->post(route('threads'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_recaptcha_verification()
    {
        if (Recaptcha::isInTestMode()) {
            $this->markTestSkipped('Recaptach is in test mode.');
        }

        // Unbind Recaptcha from the container
        // We need to verify Recaptcha works actually.
        unset(app()[Recaptcha::class]);

        $this->publishThread()
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
        create(Channel::class);

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    protected function publishThread($overrides = [])
    {
        $this->signIn();

        $thread = make(Thread::class, $overrides);

        return $this->post(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }

    /** @test */
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);
        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $this->deleteJson($thread->path())
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    /** @test */
    public function unauthorized_users_may_not_delete_threads()
    {
        $thread = create(Thread::class);

        $this->delete($thread->path())
            ->assertRedirect(route('login'));

        $this->signIn();

        // 403 Forbidden
        $this->delete($thread->path())->assertStatus(403);
    }

    /** @test */
    public function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = factory(User::class)->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make(Thread::class);

        $this->post(route('threads'), $thread->toArray())
        ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'You mush first confirm your email address.');
    }

    /** @test */
    public function a_thead_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'foo title']);

        $this->assertEquals($thread->slug, 'foo-title');

        $thread = $this->postJson(route('threads.store'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    public function a_thread_with_a_title_what_ends_in_a_number_should_generate_a_proper_slug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'He is 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("he-is-24-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    public function a_new_thread_may_not_be_created_in_an_archived_channel()
    {
        $channel = create(Channel::class, ['archived' => true]);

        $this->publishThread(['channel_id' => $channel])
        ->assertSessionHasErrors('channel_id');

        // dd(Thread::all());
        $this->assertCount(0, Thread::all());
    }
}
