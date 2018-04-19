<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Reply;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_fetch_their_most_recently_reply()
    {
        $user = create(User::class);

        $reply = create(Reply::class, ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /** @test */
    public function a_user_can_determine_their_avatar_path()
    {
        $user = create(User::class);

        $this->assertEquals(asset('images/avatars/default.svg'), $user->avatar_path);

        $user->avatar_path = 'avatars/me.png';

        $this->assertEquals(asset('storage/avatars/me.png'), $user->avatar_path);
    }
}
