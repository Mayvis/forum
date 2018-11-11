<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $this->withoutExceptionHandling();

        $kevin = create('App\User', ['name' => 'Kevin']);

        $this->signIn($kevin);

        $liang = create('App\User', ['name' => 'LiangYu']);

        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => '@LiangYu look at this. Also @Liang'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $liang->notifications);
    }
}
