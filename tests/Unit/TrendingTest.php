<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Trending;

class TrendingTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->trending = new Trending;

        $this->trending->reset();
    }

    /** @test */
    public function it_stores_trending_threads_in_redis()
    {
        $this->assertEmpty($this->trending->get());

        $this->trending->push(new FakeThread('boring title'));

        $this->trending->push(new FakeThread('popular title'));
        $this->trending->push(new FakeThread('popular title'));
        $this->trending->push(new FakeThread('popular title'));

        $this->assertCount(2, $trending = $this->trending->get());
        $this->assertEquals(['popular title', 'boring title'], array_pluck($trending, 'title'));
    }
}

class FakeThread
{
    public $title;

    public function __construct($title)
    {
        $this->title = $title;
    }

    public function path()
    {
        return 'some path';
    }
}
