<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Trending;
use PHPUnit\Framework\Assert;

class TrendingThreadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->trending = new Trending;

        // Clearing out all data before every test
        $this->trending->reset();
    }

    /** @test */
    public function a_thread_increments_its_score_each_time_it_is_read()
    {
        // $this->assertCount(0, $this->trending->get());
        $this->assertEmpty($this->trending->get());

        $thread = create(Thread::class);

        $this->call('GET', $thread->path());

        $this->assertCount(1, $trending = $this->trending->get());

        $this->assertEquals($thread->title, $trending[0]->title);
    }

    /** @test */
    public function a_thread_increments_its_score_each_time_it_is_read_with_a_fake_class()
    {
        // Whenever you require an instance of Trending
        // I want it to be an instance of FakeTrending
        // I am going to swap it out with FakeTrending
        app()->instance(Trending::class, new FakeTrending);

        $trending = app(Trending::class);

        $trending->assertEmpty();

        $thread = create(Thread::class);

        $this->call('GET', $thread->path());

        $trending->assertCount(1);

        $this->assertEquals($thread->title, $trending->threads[0]->title);
    }
}

class FakeTrending extends Trending
{
    public $threads = [];

    public function push($thread)
    {
        $this->threads[] = $thread;
    }

    public function assertEmpty()
    {
        Assert::assertEmpty($this->threads);
    }

    public function assertCount($count)
    {
        Assert::assertCount($count, $this->threads);
    }
}
