<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class AdministratorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_access_the_admin_section()
    {
        $this->signInAdmin()
            ->get(route('admin.dashboard.index'))
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function non_admins_can_not_access_the_admin_section()
    {
        $this->signIn()
            ->get(route('admin.dashboard.index'))
            ->assertStatus(403);
    }
}
