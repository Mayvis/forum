<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmedYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        event(new Registered(create('App\User')));

        Mail::assertQueued(PleaseConfirmedYourEmail::class);
    }

    /** @test */
    public function user_can_fully_confirmed_their_email_addresses()
    {
        $this->withoutExceptionHandling();

        $this->post('/register', [
            'name' => 'LiangYu',
            'email' => 'liang@example.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar',
        ]);

        $user = User::whereName('LiangYu')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get('/register/confirm?token=' . $user->confirmation_token)
            ->assertRedirect(route('threads'));

        $this->assertTrue($user->fresh()->confirmed);
    }
}
