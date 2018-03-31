<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Mockery;

class InstallCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        File::move('.env', '.env.backup');

        config(['app.key' => '']);
    }

    public function tearDown()
    {
        parent::tearDown();

        File::move('.env.backup', '.env');
    }

    /** @test */
    public function it_creates_the_environment_file()
    {
        $this->assertFileNotExists('.env');

        $this->artisan('council:install');

        $this->assertFileExists('.env');
    }

    /** @test */
    public function it_generates_an_app_key()
    {
        $this->artisan('council:install');

        $file = file_get_contents('.env');

        preg_match('/APP_KEY=(.*)/', $file, $matches);

        $this->assertStringStartsWith('base64', $matches[1]);
    }

    /** @test */
    public function it_sets_the_database_env_config()
    {
        $this->partialMock(['ask', 'secret'], function ($mock) {
            $mock->shouldReceive('ask')->with('Database name')->andReturn('myDatabase');
            $mock->shouldReceive('ask')->with('Database user')->andReturn('johndoe');
            $mock->shouldReceive('secret')->with('Database password ("null") for no password')->andReturn('password');
        });

        $this->artisan('council:install', ['--no-interaction' => true]);

        // Then we assert that in env correct values are there
        $file = file_get_contents('.env');

        $this->assertEnvKeyEquals('DB_DATABASE', 'myDatabase');
        $this->assertEnvKeyEquals('DB_USERNAME', 'johndoe');
        $this->assertEnvKeyEquals('DB_PASSWORD', 'password');
    }

    public function assertEnvKeyEquals($key, $value)
    {
        $file = file_get_contents('.env');

        preg_match("/{$key}=(.*)/", $file, $matches);

        $this->assertEquals($value, $matches[1]);
    }

    /** @test */
    public function it_optionally_migrates_the_database()
    {
        $this->partialMock(['confirm', 'call'], function ($mock) {
            $mock->shouldReceive('confirm')->once()->andReturn(true);
            $mock->shouldReceive('call')->with('key:generate');
            $mock->shouldReceive('call')->with('migrate')->once();
        });

        $this->artisan('council:install', ['--no-interaction' => true]);
    }

    protected function partialMock($methods, $assertions = null)
    {
        $assertions = $assertions ?? function () {};

        $methods = implode(',', (array) $methods);

        $command = Mockery::mock("App\Console\Commands\InstallCommand[{$methods}]", $assertions);

        app('Illuminate\Contracts\Console\Kernel')->registerCommand($command);

        return $command;
    }
}
