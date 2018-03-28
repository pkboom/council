<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AdministratorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_access_the_admin_section()
    {
        $admin = create(User::class);

        config(['council.administrator' => [$admin->email]]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertStatus(200);
    }

    /** @test */
    public function non_admins_can_not_access_the_admin_section()
    {
        $user = create(User::class);

        $this->get('/admin')->assertStatus(403);

        $this->actingAs($user)
        ->get('/admin')
        ->assertStatus(403);
    }
}