<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocakThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_administrator_can_lock_any_thread()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread');

        $thread->lock();

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
