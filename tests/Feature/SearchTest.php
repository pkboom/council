<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads()
    {
        // In a test mode, when we create threads,
        // Algolia tries to add them into the index, making tests super slow.
        // So when we test, we don't want it to work.
        // In phpunit.xml, we set SCOUT_DRIVER to null.
        // Only here it will work.
        config(['scout.driver' => 'algolia']);

        create(Thread::class, [], 2);
        create(Thread::class, ['body' => 'A thread with the foobar term.'], 2);

        do {
            // Account for latency.
            sleep(.25);

            $results = $this->getJson('/threads/search?q=foobar')->json()['data'];
        } while (empty($results));

        $this->assertCount(2, $results);

        Thread::latest()->take(4)->unsearchable();
    }
}
