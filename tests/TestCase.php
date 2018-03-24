<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        // Foreign key constraints are not enabled by default with sqlite
        // With mysql, everything is fine.
        Schema::enableForeignKeyConstraints();
        // DB::statement('PRAGMA foreign_keys = ON;');
    }

    protected function signIn($user = null)
    {
        $user = $user ?? create(User::class);

        return $this->actingAs($user);
    }
}
