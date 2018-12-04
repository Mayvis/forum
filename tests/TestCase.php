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
        $admin = $admin ?: factory('App\User')
            ->states('administrator')
            ->create();

        return $this->actingAs($admin);
    }
}
