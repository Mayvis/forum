<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PinThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function administrators_can_pin_threads()
    {
        $this->withoutExceptionHandling();

        $this->signInAdmin();

        $thread = create('App\Thread');

        $this->post(route('pinned-threads.store', $thread));

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that threads was pinned.');
    }

    /** @test */
    public function administrators_can_unpin_threads()
    {
        $this->withoutExceptionHandling();

        $this->signInAdmin();

        $thread = create('App\Thread', ['pinned' => true]);

        $this->delete(route('pinned-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->pinned, 'Failed asserting that threads was unlocked.');
    }

    /** @test */
    public function pinned_threads_are_listed_first()
    {
        $threads = create('App\Thread', [], 3);

        $ids = $threads->pluck('id');

        $this->signInAdmin();

        $this->getJson(route('threads'))
            ->assertJson([
                'data' => [
                    ['id' => $ids[0]],
                    ['id' => $ids[1]],
                    ['id' => $ids[2]],
                ]
            ]);

        $this->post(route('pinned-threads.store', $pinned = $threads->last()));
        $this->getJson(route('threads'))
            ->assertJson([
                "data" => [
                    ["id" => $pinned->id],
                    ["id" => $ids[0]],
                    ["id" => $ids[1]],
                ]
            ]);
    }
}
