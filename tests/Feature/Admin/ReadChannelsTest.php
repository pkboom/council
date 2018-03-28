<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class ReadChannelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_may_access_the_admin_channel_section()
    {
        $admin = create(User::class);

        config(['council.administrator' => [$admin->email]]);

        $this->actingAs($admin)
            ->get(route('admin.channels.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function non_admins_can_not_access_the_admin_channel_section()
    {
        $user = create(User::class);

        $this->get(route('admin.channels.index'))->assertStatus(403);

        $this->actingAs($user)
        ->get(route('admin.channels.index'))
        ->assertStatus(403);
    }
}
