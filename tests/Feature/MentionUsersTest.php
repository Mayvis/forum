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

    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        create('App\User', ['name' => 'LiangYu']);
        create('App\User', ['name' => 'Liangoo']);
        create('App\User', ['name' => 'LianLove']);

        $results = $this->json('GET', '/api/users', ['name' => 'Liang']);

        $this->assertCount(2, $results->json());
    }
}
