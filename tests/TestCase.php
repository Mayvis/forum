<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function signIn($user = null)
    {
        $user = $user ?: create('App\User');

        return $this->actingAs($user);
    }

    protected function signInAdmin($admin = null)
    {
        $admin = $admin ?: create('App\User');

        config(['forum.administrators' => [$admin->email]]);

        return $this->actingAs($admin);
    }
}
