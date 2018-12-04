<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads()
    {
        if ( ! config('scout.algolia.id')) {
            $this->markTestSkipped("Algolia is not configured.");
            return true;
        }

        $this->withoutExceptionHandling();

        config(['scout.driver' => 'algolia']);

        $search = 'LiangYu';

        create('App\Thread', [], 2);
        create('App\Thread', ['body' => "A thread with {$search} term."], 2);

        do {
            sleep(.25);

            $results = $this->getJson("/threads/search?q={$search}")->json()['data'];
        } while(empty($results));

        $this->assertCount(2, $results);

        Thread::latest()->take(4)->unsearchable();
    }
}
