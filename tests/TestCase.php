<?php

namespace Tests;

use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        // Foreign key constraints are not enabled by default with sqlite
        // With mysql, everything is fine.
        Schema::enableForeignKeyConstraints();
    }

    protected function signIn($user = null)
    {
        $user = $user ?? create(User::class);

        return $this->actingAs($user);
    }

    protected function signInAdmin($admin = null)
    {
        $admin = $admin ?? create(User::class);

        config(['council.administrators' => [$admin->email]]);

        return $this->actingAs($admin);
    }
}
