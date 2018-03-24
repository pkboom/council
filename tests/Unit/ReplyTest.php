<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Reply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Carbon\Carbon;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    protected $reply;

    public function setUp()
    {
        parent::setUp();

        $this->reply = create(Reply::class);
    }

    /** @test */
    public function a_reply_has_an_owner()
    {
        $this->assertInstanceOf(User::class, $this->reply->owner);
    }

    /** @test */
    public function a_reply_knows_if_it_is_just_published()
    {
        $this->assertTrue($this->reply->wasJustPublished());

        $this->reply->created_at = Carbon::now()->subHour();

        $this->assertFalse($this->reply->wasJustPublished());
    }

    /** @test */
    public function a_reply_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = new Reply([
            'body' => '@johndoe wants to talk to @janedoe'
        ]);

        $users = $reply->mentionedUsers();

        $this->assertEquals(['johndoe', 'janedoe'], $users);
    }

    /** @test */
    public function it_wraps_mentioned_users_in_the_body_within_anchor_tags()
    {
        $reply = new Reply([
            'body' => 'Hey @Jane-Doe'
        ]);

        $this->assertEquals(
            'Hey <a href="/profiles/Jane-Doe">@Jane-Doe</a>',
            $reply->body
        );
    }

    /** @test */
    public function it_knows_if_it_is_the_best()
    {
        $this->assertFalse($this->reply->isBest);

        $this->reply->thread->update(['best_reply_id' => $this->reply->id]);

        $this->assertTrue($this->reply->fresh()->isBest);
    }
}
