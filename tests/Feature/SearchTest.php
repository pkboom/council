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
        if (!config('scout.algolia.id')) {
            $this->markTestSkipped('Algolia is not configured.');
        }

        config(['scout.driver' => 'algolia']);

        create(Thread::class, [], 2);
        create(Thread::class, ['body' => 'A thread with the foobar term.'], 2);

        do {
            // Account for latency.
            sleep(.5);

            // $results = $this->getJson('/threads/search?q=foobar')->json()['data'];
            $results = $this->getJson(route('search.show', ['q' => 'foobar']))->json()['data'];
        } while (empty($results));

        $this->assertCount(2, $results);

        // Clean up.
        Thread::latest()->take(4)->unsearchable();
    }
}
